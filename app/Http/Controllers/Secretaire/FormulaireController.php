<?php

namespace App\Http\Controllers\Secretaire;

use App\Events\DossierMisAJour;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreformulaireRequest;
use App\Http\Requests\UpdateformulaireRequest;
use App\Models\Formulaire;
use App\Models\User;
use App\Models\WorkflowLog;
use App\Notifications\DossierEnvoyeAuChef;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $query = Formulaire::query()->latest();
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

    public function store(StoreformulaireRequest $request): RedirectResponse
    {
        $this->authorize('create', Formulaire::class);

        $data = $request->validated();

        if ($request->type_document === 'Autre' && $request->filled('autre_type_document')) {
            $data['type_document'] = $request->autre_type_document;
        }

        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        unset($data['status'], $data['autre_type_document']);

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

        User::role('chef_de_service')->get()->each(function (User $chef) use ($formulaire): void {
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
