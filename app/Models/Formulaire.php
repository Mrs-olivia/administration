<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Formulaire extends Model
{
    /** @use HasFactory<\Database\Factories\FormulaireFactory> */
    use HasFactory;

    public const STATUS_EN_ATTENTE = 0;

    public const STATUS_EN_COURS = 1;

    public const STATUS_TRAITE = 2;

    public const STATUS_REJETE = 3;

    public const STATUS_ARCHIVE = 4;

    protected $fillable = [
        'created_by_user_id',
        'service_code',
        'origine_service_code',
        'origine_reference',
        'transfers_count',
        'first_transferred_at',
        'last_transferred_at',
        'transfer_requires_secretary_edit',
        'annee',
        'numero_ordre',
        'expediteur',
        'objet',
        'type_document',
        'date_reception',
        'date_echeance',
        'fichier',
        'status',
        'note',
        'annotation_chef',
        'chef_annotations_log',
        'last_decision_chef_service_code',
        'sent_to_chef_at',
    ];

    protected $casts = [
        'date_reception' => 'date',
        'date_echeance' => 'date',
        'status' => 'integer',
        'transfers_count' => 'integer',
        'sent_to_chef_at' => 'datetime',
        'first_transferred_at' => 'datetime',
        'last_transferred_at' => 'datetime',
        'transfer_requires_secretary_edit' => 'boolean',
        'chef_annotations_log' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getReferenceAttribute(): string
    {
        return "{$this->service_code}/{$this->annee}-".str_pad((string) $this->numero_ordre, 4, '0', STR_PAD_LEFT);
    }

    public function serviceLabel(): string
    {
        $services = config('administration.services', []);

        return $services[$this->service_code] ?? $this->service_code;
    }

    public function origineServiceLabel(): ?string
    {
        if (blank($this->origine_service_code)) {
            return null;
        }

        $services = config('administration.services', []);

        return $services[$this->origine_service_code] ?? $this->origine_service_code;
    }

    /**
     * Service qui détient le droit de clôture (archivage) et reste identifiable sur tout le parcours.
     * Dès le premier transfert, origine_service_code pointe vers ce service (référence d’origine conservée).
     *
     * Important : utiliser filled() et pas seulement ?? car une chaîne vide en base ferait croire
     * à un « relais » (initiateur vide) et bloquerait tout transfert.
     */
    public function initiatingServiceCode(): string
    {
        $fromOrigine = trim((string) ($this->origine_service_code ?? ''));
        if ($fromOrigine !== '') {
            return $fromOrigine;
        }

        return trim((string) $this->service_code);
    }

    public function initiatingServiceLabel(): string
    {
        $code = $this->initiatingServiceCode();
        $services = config('administration.services', []);

        return $services[$code] ?? $code;
    }

    /** Le dossier est physiquement chez le service initiateur (pas en relais). */
    public function isHeldByInitiatingService(): bool
    {
        return trim((string) $this->service_code) === $this->initiatingServiceCode();
    }

    /** Dossier pris en charge par un service relais (transmis depuis l’initiateur ou un tiers). */
    public function isRelayServiceHold(): bool
    {
        return ! $this->isHeldByInitiatingService();
    }

    /**
     * Prochain numéro d’ordre (transaction + verrou sur une ligne de séquence).
     * PostgreSQL n’accepte pas MAX(...) avec FOR UPDATE ; on utilise une table dédiée.
     */
    public static function nextNumeroOrdre(string $serviceCode, int $annee): int
    {
        return (int) DB::transaction(function () use ($serviceCode, $annee): int {
            $lastError = null;
            for ($i = 0; $i < 12; $i++) {
                try {
                    return self::allocateNextUsingSequenceTable($serviceCode, $annee);
                } catch (QueryException $e) {
                    if (! self::isUniqueConstraintViolation($e)) {
                        throw $e;
                    }
                    $lastError = $e;
                }
            }

            throw $lastError ?? new \RuntimeException('Impossible d’attribuer un numéro d’ordre.');
        });
    }

    /**
     * @throws QueryException en cas de conflit d’insertion unique (retry en amont).
     */
    private static function allocateNextUsingSequenceTable(string $serviceCode, int $annee): int
    {
        $seq = DB::table('formulaire_reference_sequences')
            ->where('service_code', $serviceCode)
            ->where('annee', $annee)
            ->lockForUpdate()
            ->first();

        if ($seq === null) {
            $max = (int) self::query()
                ->where('service_code', $serviceCode)
                ->where('annee', $annee)
                ->max('numero_ordre');
            $next = $max + 1;
            DB::table('formulaire_reference_sequences')->insert([
                'service_code' => $serviceCode,
                'annee' => $annee,
                'last_num' => $next,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $next;
        }

        $next = (int) $seq->last_num + 1;
        DB::table('formulaire_reference_sequences')
            ->where('id', $seq->id)
            ->update(['last_num' => $next, 'updated_at' => now()]);

        return $next;
    }

    private static function isUniqueConstraintViolation(QueryException $e): bool
    {
        $info = $e->errorInfo;
        if (($info[0] ?? '') === '23505') {
            return true;
        }
        if (($info[1] ?? null) === 1062) {
            return true;
        }
        if (($info[1] ?? null) === 19) {
            return true;
        }

        return str_contains(strtolower($e->getMessage()), 'unique constraint');
    }

    /** Aperçu sans verrou — peut différer légèrement du numéro réel sous forte concurrence. */
    public static function peekNextNumeroOrdre(string $serviceCode, int $annee): int
    {
        $maxForm = (int) self::query()
            ->where('service_code', $serviceCode)
            ->where('annee', $annee)
            ->max('numero_ordre');

        $lastSeq = (int) (DB::table('formulaire_reference_sequences')
            ->where('service_code', $serviceCode)
            ->where('annee', $annee)
            ->value('last_num') ?? 0);

        return max($maxForm, $lastSeq) + 1;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_EN_ATTENTE => 'En attente',
            self::STATUS_EN_COURS => 'En cours de traitement',
            self::STATUS_TRAITE => 'Traité / Validé',
            self::STATUS_REJETE => 'Rejeté',
            self::STATUS_ARCHIVE => 'Archivé',
            default => 'Inconnu',
        };
    }

    /** Le secrétaire peut modifier tant que le chef n’a pas pris le dossier en charge (statut encore « en attente »). */
    public function isEditableBySecretaire(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    /**
     * Archivage réservé au service initiateur, après une décision chef rendue **sur ce même service**
     * (après un retour de circuit, une nouvelle validation / rejet du chef initial est requise).
     */
    public function canBeArchivedBySecretaire(): bool
    {
        if (! $this->isHeldByInitiatingService()) {
            return false;
        }

        $status = (int) $this->status;
        if (! in_array($status, [self::STATUS_TRAITE, self::STATUS_REJETE], true)) {
            return false;
        }

        $last = trim((string) ($this->last_decision_chef_service_code ?? ''));

        return $last !== '' && $last === $this->initiatingServiceCode();
    }

    /** Texte d’aide lorsque le bouton Archiver est désactivé (secrétariat initiateur). */
    public function archiveDisabledHintForSecretaire(): string
    {
        if ($this->isHeldByInitiatingService()
            && in_array((int) $this->status, [self::STATUS_TRAITE, self::STATUS_REJETE], true)
            && ! $this->canBeArchivedBySecretaire()) {
            return 'Une nouvelle décision du chef du service initiateur est requise avant clôture (dossier revenu d’un circuit ou décision enregistrée ailleurs).';
        }

        return 'Archivage réservé au service initiateur, après décision du chef (traité ou rejeté), y compris une ultime décision après retour du dossier.';
    }

    public function canBeSentToChef(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    public function canBeTransferredToOtherService(): bool
    {
        $status = (int) $this->status;

        if (! in_array($status, [self::STATUS_TRAITE, self::STATUS_REJETE], true)) {
            return false;
        }

        // Après rejet du chef du service d’origine sur un dossier déjà passé par un circuit inter-services :
        // le secrétariat doit modifier le dossier avant tout nouveau transfert.
        if ($this->transfer_requires_secretary_edit) {
            return false;
        }

        // Rejet lors de la validation interne au service d’origine, sans circulation : pas de transfert sortant.
        if ($status === self::STATUS_REJETE
            && $this->isHeldByInitiatingService()
            && ((int) $this->transfers_count) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Cible autorisée pour un transfert : initiateur → tout autre service ;
     * service relais (2e, 3e…) → tout autre service sauf soi (renvoi à l’origine ou saut vers un autre relais).
     */
    public function isAllowedTransferTarget(string $targetServiceCode): bool
    {
        $target = trim($targetServiceCode);
        $here = trim((string) $this->service_code);

        if ($target === '' || $target === $here) {
            return false;
        }

        if (! $this->canBeTransferredToOtherService()) {
            return false;
        }

        return true;
    }

    public function hasTransferTrace(): bool
    {
        return ((int) $this->transfers_count) > 0;
    }

    /** Texte chef déjà présent (annotation ou décision) — pour règle « commentaire obligatoire si vide ». */
    public function hasChefAnnotation(): bool
    {
        if (filled(trim((string) $this->annotation_chef))) {
            return true;
        }

        $log = $this->chef_annotations_log;

        return is_array($log) && $log !== [];
    }

    /**
     * Ajoute une entrée au journal d’annotations et régénère le champ affiché (nomenclature [CODE | date] …).
     *
     * @param  'note'|'validation'|'rejet'  $kind
     */
    public function appendChefAnnotationEntry(string $serviceCode, int $userId, string $kind, string $body): void
    {
        $log = $this->chef_annotations_log ?? [];
        $log[] = [
            'service_code' => $serviceCode,
            'user_id' => $userId,
            'kind' => $kind,
            'body' => $body,
            'created_at' => now()->toIso8601String(),
        ];
        $this->chef_annotations_log = $log;
        $this->rebuildAnnotationChefDisplay();
    }

    public function rebuildAnnotationChefDisplay(): void
    {
        $log = $this->chef_annotations_log ?? [];
        if ($log === []) {
            $this->annotation_chef = null;

            return;
        }

        $blocks = [];
        foreach ($log as $entry) {
            $code = $entry['service_code'] ?? '';
            $at = isset($entry['created_at'])
                ? Carbon::parse($entry['created_at'])->format('d/m/Y H:i')
                : '';
            $kind = $entry['kind'] ?? 'note';
            $label = match ($kind) {
                'validation' => 'Validation',
                'rejet' => 'Rejet',
                default => 'Annotation',
            };
            $body = trim((string) ($entry['body'] ?? ''));
            $blocks[] = '['.$code.' | '.$at.'] '.$label.' — '.$body;
        }
        $this->annotation_chef = implode("\n\n", $blocks);
    }

    /** Variante UI pour badges (polling / Alpine). */
    public function statusBadgeVariant(): string
    {
        return match ($this->status) {
            self::STATUS_EN_ATTENTE => 'yellow',
            self::STATUS_EN_COURS => 'blue',
            self::STATUS_TRAITE => 'green',
            self::STATUS_REJETE => 'red',
            self::STATUS_ARCHIVE => 'gray',
            default => 'gray',
        };
    }

    /** Payload JSON pour rafraîchissement secrétaire (SSOT). */
    public function toPollPayload(): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'origine_reference' => $this->origine_reference,
            'transfers_count' => (int) $this->transfers_count,
            'last_transferred_at' => $this->last_transferred_at?->toIso8601String(),
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'status_badge_variant' => $this->statusBadgeVariant(),
            'annotation_chef' => $this->annotation_chef,
            'initiating_service_code' => $this->initiatingServiceCode(),
            'last_decision_chef_service_code' => $this->last_decision_chef_service_code,
            'sent_to_chef_at' => $this->sent_to_chef_at?->toIso8601String(),
            'transfer_requires_secretary_edit' => (bool) $this->transfer_requires_secretary_edit,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
