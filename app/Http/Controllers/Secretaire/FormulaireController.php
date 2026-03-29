<?php

namespace App\Http\Controllers\Secretaire;

use App\Events\DossierMisAJour;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreformulaireRequest;
use App\Http\Requests\TransferFormulaireRequest;
use App\Http\Requests\UpdateformulaireRequest;
use App\Models\Formulaire;
use App\Models\User;
use App\Models\WorkflowLog;
use App\Notifications\DossierEnvoyeAuChef;
use App\Notifications\DossierTransfereVersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FormulaireController extends Controller
{
    /**
     * Polling (liste / tableau de bord) : source unique de vérité = table formulaires.
     */
    public function poll(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Formulaire::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'max:50'],
            'ids.*' => ['integer', 'exists:formulaires,id'],
        ]);

        $user = $request->user();
        $items = [];

        foreach (Formulaire::whereIn('id', $validated['ids'])->get() as $formulaire) {
            if (! $user->can('view', $formulaire)) {
                continue;
            }
            $items[$formulaire->id] = $formulaire->toPollPayload();
        }

        return response()->json(['items' => $items]);
    }

    /**
     * Polling (fiche dossier) pour mise à jour en temps quasi réel du statut et du commentaire chef.
     */
    public function pollShow(Formulaire $formulaire): JsonResponse
    {
        $this->authorize('view', $formulaire);

        return response()->json($formulaire->fresh()->toPollPayload());
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Formulaire::class);

        $allowedStatuses = [
            Formulaire::STATUS_EN_ATTENTE,
            Formulaire::STATUS_EN_COURS,
            Formulaire::STATUS_TRAITE,
            Formulaire::STATUS_REJETE,
            Formulaire::STATUS_ARCHIVE,
        ];

        $query = Formulaire::query();

        if ($request->user()->service_code) {
            $query->where('service_code', $request->user()->service_code);
        }

        if ($request->boolean('transferred')) {
            $query->where('transfers_count', '>', 0)
                ->orderByDesc('last_transferred_at');
        } else {
            $query->latest();
        }

        $status = $request->query('status');
        if ($status !== null) {
            $statusInt = (int) $status;
            if (in_array($statusInt, $allowedStatuses, true)) {
                $query->where('status', $statusInt);
            }
        }

        $formulaires = $query->paginate(15)->withQueryString();

        return view('secretaire.formulaire.index', compact('formulaires'));
    }

    public function nextReferencePreview(Request $request): JsonResponse
    {
        $this->authorize('create', Formulaire::class);

        $codes = array_keys(config('administration.services', []));
        $validated = $request->validate([
            'service_code' => ['required', 'string', 'max:10', Rule::in($codes)],
            'annee' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $user = $request->user();
        if ($user->service_code && $user->service_code !== $validated['service_code']) {
            abort(403);
        }

        $annee = (int) ($validated['annee'] ?? date('Y'));
        $serviceCode = $validated['service_code'];
        $next = Formulaire::peekNextNumeroOrdre($serviceCode, $annee);
        $reference = "{$serviceCode}/{$annee}-".str_pad((string) $next, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'annee' => $annee,
            'next_numero_ordre' => $next,
            'reference_preview' => $reference,
            'service_label' => config('administration.services')[$serviceCode] ?? $serviceCode,
        ]);
    }

    public function store(StoreformulaireRequest $request): RedirectResponse
    {
        $this->authorize('create', Formulaire::class);

        $request->mergeIfMissing(['annee' => (int) date('Y')]);
        if ($request->user()->service_code) {
            $request->merge(['service_code' => $request->user()->service_code]);
        }

        $data = $request->validated();

        if ($request->user()->service_code && $data['service_code'] !== $request->user()->service_code) {
            abort(403);
        }

        if ($request->type_document === 'Autre' && $request->filled('autre_type_document')) {
            $data['type_document'] = $request->autre_type_document;
        }

        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        unset($data['status'], $data['autre_type_document']);

        $annee = (int) $data['annee'];
        $serviceCode = $data['service_code'];
        $data['annee'] = $annee;
        $data['numero_ordre'] = Formulaire::nextNumeroOrdre($serviceCode, $annee);
        $data['status'] = Formulaire::STATUS_EN_ATTENTE;
        $data['created_by_user_id'] = $request->user()->id;

        $formulaire = Formulaire::create($data);

        $this->logWorkflow($formulaire, 'secretaire_formulaire_created', [
            'type_document' => $formulaire->type_document,
        ]);

        return redirect()->route('secretaire.forms.index')->with('success', 'Dossier créé. Statut : En attente. Vous pouvez le modifier ou l’envoyer au chef.');
    }

    public function show(Formulaire $formulaire): View
    {
        $this->authorize('view', $formulaire);

        return view('secretaire.formulaire.show', compact('formulaire'));
    }

    public function edit(Formulaire $formulaire): View
    {
        $this->authorize('update', $formulaire);

        return view('secretaire.formulaire.edit', compact('formulaire'));
    }

    public function update(UpdateformulaireRequest $request, Formulaire $formulaire): RedirectResponse
    {
        $this->authorize('update', $formulaire);
        $data = $request->validated();

        if (($data['type_document'] ?? null) === 'Autre' && $request->filled('autre_type_document')) {
            $data['type_document'] = $request->autre_type_document;
        }

        unset($data['autre_type_document']);

        if ($request->hasFile('fichier')) {
            if ($formulaire->fichier) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($formulaire->fichier);
            }
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        $formulaire->update($data);

        $this->logWorkflow($formulaire, 'secretaire_formulaire_updated', [
            'updated_fields' => array_keys($data),
        ]);

        return redirect()->route('secretaire.forms.index')->with('success', 'Dossier mis à jour.');
    }

    public function destroy(Formulaire $formulaire): RedirectResponse
    {
        $this->authorize('delete', $formulaire);

        if ($formulaire->fichier) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($formulaire->fichier);
        }

        $formulaire->delete();

        return redirect()->route('secretaire.forms.index')->with('success', 'Dossier supprimé.');
    }

    public function send(Formulaire $formulaire): RedirectResponse
    {
        $this->authorize('send', $formulaire);

        $alreadySentToChef = $formulaire->sent_to_chef_at !== null;

        if (! $alreadySentToChef) {
            $formulaire->sent_to_chef_at = now();
            $formulaire->save();
        }

        User::chefsNotifiablesPourService($formulaire->service_code)
            ->each(function (User $chef) use ($formulaire): void {
                $chef->notify(new DossierEnvoyeAuChef($formulaire));
            });

        $formulaire = $formulaire->fresh();

        event(new DossierMisAJour($formulaire));

        $this->logWorkflow(
            $formulaire,
            $alreadySentToChef ? 'secretaire_formulaire_resent_to_chef' : 'secretaire_formulaire_sent_to_chef'
        );

        $message = $alreadySentToChef
            ? 'Une nouvelle notification a été envoyée au chef.'
            : 'Le chef a été notifié. Le dossier reste « En attente » jusqu’à son ouverture.';

        return redirect()->back()->with('success', $message);
    }

    public function archive(Formulaire $formulaire): RedirectResponse
    {
        $this->authorize('archive', $formulaire);

        $formulaire->status = Formulaire::STATUS_ARCHIVE;
        $formulaire->save();

        event(new DossierMisAJour($formulaire->fresh()));

        $this->logWorkflow($formulaire, 'secretaire_formulaire_archived');

        return redirect()->back()->with('success', 'Dossier archivé.');
    }

    public function transfer(TransferFormulaireRequest $request, Formulaire $formulaire): RedirectResponse
    {
        $target = $request->validated('target_service_code');
        $fromService = trim((string) $formulaire->service_code);
        $initiatingBefore = $formulaire->initiatingServiceCode();
        $wasRelayHolder = $formulaire->isRelayServiceHold();

        $oldRef = $formulaire->reference;
        $annee = (int) $formulaire->annee;
        $next = Formulaire::nextNumeroOrdre($target, $annee);

        if (blank($formulaire->origine_reference)) {
            $formulaire->origine_service_code = $formulaire->service_code;
            $formulaire->origine_reference = $oldRef;
        }

        $stamp = '[Transféré depuis '.$oldRef.' le '.now()->format('d/m/Y H:i').']';
        $formulaire->note = trim(trim((string) $formulaire->note).' '.$stamp);

        $formulaire->service_code = $target;
        $formulaire->numero_ordre = $next;
        $formulaire->status = Formulaire::STATUS_EN_ATTENTE;
        $formulaire->sent_to_chef_at = null;
        $formulaire->last_decision_chef_service_code = null;

        $formulaire->transfers_count = ((int) $formulaire->transfers_count) + 1;
        if ($formulaire->first_transferred_at === null) {
            $formulaire->first_transferred_at = now();
        }
        $formulaire->last_transferred_at = now();

        $formulaire->save();

        User::secretairesNotifiablesPourService($target)
            ->each(function (User $secretaire) use ($formulaire): void {
                $secretaire->notify(new DossierTransfereVersService($formulaire));
            });

        event(new DossierMisAJour($formulaire->fresh()));

        $fresh = $formulaire->fresh();
        $init = $initiatingBefore;
        $stepType = match (true) {
            $target === $init && $fromService !== $init => 'retour_service_initiateur',
            $fromService === $init && $target !== $init => 'depuis_service_initiateur',
            default => 'transfert_inter_services',
        };

        $this->logWorkflow($fresh, 'secretaire_formulaire_transferred', [
            'from_service_code' => $fromService,
            'to_service_code' => $target,
            'from_reference' => $oldRef,
            'to_reference' => $fresh->reference,
            'initiating_service_code' => $fresh->initiatingServiceCode(),
            'detenteur_etait_relais' => $wasRelayHolder,
            'type_etape' => $stepType,
            'transfers_count' => $fresh->transfers_count,
        ]);

        $label = config('administration.services')[$target] ?? $target;
        $newRef = $formulaire->reference;

        return redirect()
            ->back()
            ->with(
                'success',
                'Dossier transmis au service « '.$label.' ». Nouvelle référence : '.$newRef.'. Vous pouvez prévenir le chef depuis la fiche si besoin.'
            );
    }

    private function logWorkflow(Formulaire $formulaire, string $action, array $details = []): void
    {
        WorkflowLog::create([
            'formulaire_id' => $formulaire->id,
            'user_id' => auth()->id(),
            'user_role' => auth()->user()?->role,
            'action' => $action,
            'details' => $details !== [] ? $details : null,
        ]);
    }
}
