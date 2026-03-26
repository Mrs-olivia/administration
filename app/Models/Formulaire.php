<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'sent_to_chef_at',
    ];

    protected $casts = [
        'date_reception' => 'date',
        'date_echeance' => 'date',
        'status' => 'integer',
        'sent_to_chef_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getReferenceAttribute(): string
    {
        return "{$this->service_code}/{$this->annee}-".str_pad((string) $this->numero_ordre, 4, '0', STR_PAD_LEFT);
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

    public function canBeArchivedBySecretaire(): bool
    {
        return in_array($this->status, [self::STATUS_TRAITE, self::STATUS_REJETE], true);
    }

    public function canBeSentToChef(): bool
    {
        return $this->status === self::STATUS_EN_ATTENTE;
    }

    /** Texte chef déjà présent (annotation ou décision) — pour règle « commentaire obligatoire si vide ». */
    public function hasChefAnnotation(): bool
    {
        return filled(trim((string) $this->annotation_chef));
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
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'status_badge_variant' => $this->statusBadgeVariant(),
            'annotation_chef' => $this->annotation_chef,
            'sent_to_chef_at' => $this->sent_to_chef_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

