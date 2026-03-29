<?php

namespace App\Http\Controllers\ChefService;

use App\Events\DossierMisAJour;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreformulaireRequest;
use App\Models\Formulaire;
use App\Models\User;
use App\Models\WorkflowLog;
use App\Notifications\DecisionChefSurDossier;
use App\Notifications\DossierEnvoyeAuSecretaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FormulaireController extends Controller
{
    public function index(Request $request): View
    {
        $allowedStatuses = [
            Formulaire::STATUS_EN_ATTENTE,
            Formulaire::STATUS_EN_COURS,
            Formulaire::STATUS_TRAITE,
            Formulaire::STATUS_REJETE,
            Formulaire::STATUS_ARCHIVE,
        ];

        $query = Formulaire::query()->latest();

        if ($request->user()->service_code) {
            $query->where('service_code', $request->user()->service_code);
        }

        $status = $request->query('status');
        if ($status !== null) {
            $statusInt = (int) $status;
            if (in_array($statusInt, $allowedStatuses, true)) {
                $query->where('status', $statusInt);
            }
        } else {
            // Par défaut côté chef : on masque les archives.
            $query->where('status', '!=', Formulaire::STATUS_ARCHIVE);
        }

        $formulaires = $query->paginate(15)->withQueryString();

        return view('chefService.formulaire.index', compact('formulaires'));
    }

    /**
     * Formulaires créés par le chef (envoi au secrétariat).
     */
    public function created(Request $request): View
    {
        $allowedStatuses = [
            Formulaire::STATUS_EN_ATTENTE,
            Formulaire::STATUS_EN_COURS,
            Formulaire::STATUS_TRAITE,
            Formulaire::STATUS_REJETE,
            Formulaire::STATUS_ARCHIVE,
        ];

        $query = Formulaire::query()
            ->where('created_by_user_id', $request->user()->id)
            ->latest();

        if ($request->user()->service_code) {
            $query->where('service_code', $request->user()->service_code);
        }

        $status = $request->query('status');
        if ($status !== null) {
            $statusInt = (int) $status;
            if (in_array($statusInt, $allowedStatuses, true)) {
                $query->where('status', $statusInt);
            }
        }

        $formulaires = $query->paginate(15)->withQueryString();

        return view('chefService.formulaire.created', compact('formulaires'));
    }

    public function store(StoreformulaireRequest $request): RedirectResponse
    {
        $this->authorize('create', Formulaire::class);

        if (! $request->user()->service_code) {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_error', true);
        }

        $annee = (int) $request->input('annee', date('Y'));
        $request->merge([
            'service_code' => $request->user()->service_code,
            'annee' => $annee,
        ]);

        $data = $request->validated();

        if ($request->type_document === 'Autre' && $request->filled('autre_type_document')) {
            $data['type_document'] = $request->autre_type_document;
        }

        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        unset($data['status'], $data['autre_type_document']);

        $data['numero_ordre'] = Formulaire::nextNumeroOrdre($data['service_code'], (int) $data['annee']);
        $data['status'] = Formulaire::STATUS_EN_ATTENTE;
        $data['created_by_user_id'] = $request->user()->id;

        $formulaire = Formulaire::create($data);

        User::secretairesNotifiablesPourService($formulaire->service_code)
            ->each(function (User $secretaire) use ($formulaire): void {
                $secretaire->notify(new DossierEnvoyeAuSecretaire($formulaire));
            });

        event(new DossierMisAJour($formulaire->fresh()));

        $this->logWorkflow($formulaire, 'chef_formulaire_created', [
            'type_document' => $formulaire->type_document,
        ]);

        return redirect()
            ->route('chefService.forms.created')
            ->with('success', 'Formulaire créé et envoyé au secrétariat. Le secrétariat pourra uniquement l’archiver.');
    }

    public function show(Formulaire $formulaire): View
    {
        $this->authorize('view', $formulaire);

        if ($formulaire->status === Formulaire::STATUS_EN_ATTENTE) {
            $formulaire->status = Formulaire::STATUS_EN_COURS;
            $formulaire->save();
            event(new DossierMisAJour($formulaire->fresh()));

            $this->logWorkflow($formulaire, 'chef_status_set_in_progress');
        }

        return view('chefService.formulaire.show', compact('formulaire'));
    }

    /**
     * Annotation optionnelle — même ligne en base (annotation_chef), SSOT.
     */
    public function annoter(Request $request, Formulaire $formulaire): RedirectResponse
    {
        $this->authorize('agirCommeChef', $formulaire);

        $request->validate([
            'annotation' => ['nullable', 'string', 'max:65535'],
        ]);

        $texte = trim((string) $request->input('annotation', ''));
        if ($texte !== '') {
            $serviceCode = (string) $request->user()->service_code;
            $formulaire->appendChefAnnotationEntry($serviceCode, (int) $request->user()->id, 'note', $texte);
            $formulaire->save();
            event(new DossierMisAJour($formulaire->fresh()));

            $this->logWorkflow($formulaire, 'chef_formulaire_annotated', [
                'service_code' => $serviceCode,
                'annotation_length' => mb_strlen($texte),
            ]);

            return redirect()->route('chefService.forms.show', $formulaire)->with('success', 'Annotation enregistrée (préfixée ['.$serviceCode.']).');
        }

        return redirect()->route('chefService.forms.show', $formulaire)->with('info', 'Saisissez un texte pour enregistrer une annotation.');
    }

    public function valider(Request $request, Formulaire $formulaire): RedirectResponse
    {
        $this->authorize('agirCommeChef', $formulaire);

        $hasPrior = $formulaire->hasChefAnnotation();

        $request->validate([
            'commentaire' => [
                Rule::requiredIf(! $hasPrior),
                'nullable',
                'string',
                'max:65535',
            ],
        ], [
            'commentaire.required' => 'Un commentaire est obligatoire lorsqu’aucune annotation n’a encore été enregistrée sur ce dossier.',
        ]);

        $final = trim((string) $request->input('commentaire', ''));
        $serviceCode = (string) $request->user()->service_code;
        $userId = (int) $request->user()->id;

        if ($final !== '') {
            $formulaire->appendChefAnnotationEntry($serviceCode, $userId, 'validation', $final);
        } elseif ($hasPrior) {
            $formulaire->appendChefAnnotationEntry(
                $serviceCode,
                $userId,
                'validation',
                'Décision validée ; annotations et commentaires précédents conservés.'
            );
        } else {
            return redirect()->back()->withErrors(['commentaire' => 'Veuillez saisir un commentaire.']);
        }

        $formulaire->status = Formulaire::STATUS_TRAITE;
        $formulaire->last_decision_chef_service_code = trim((string) $formulaire->service_code);
        $formulaire->save();

        event(new DossierMisAJour($formulaire->fresh()));
        $this->notifierSecretaire($formulaire, 'valide');

        $this->logWorkflow($formulaire, 'chef_formulaire_validated', [
            'service_code' => $serviceCode,
        ]);

        return redirect()->route('chefService.forms.show', $formulaire)->with('success', 'Dossier validé. Le secrétariat a été notifié.');
    }

    public function rejeter(Request $request, Formulaire $formulaire): RedirectResponse
    {
        $this->authorize('agirCommeChef', $formulaire);

        $hasPrior = $formulaire->hasChefAnnotation();

        $request->validate([
            'commentaire' => [
                Rule::requiredIf(! $hasPrior),
                'nullable',
                'string',
                'max:65535',
            ],
        ], [
            'commentaire.required' => 'Un commentaire (motif) est obligatoire lorsqu’aucune annotation n’a encore été enregistrée.',
        ]);

        $final = trim((string) $request->input('commentaire', ''));
        $serviceCode = (string) $request->user()->service_code;
        $userId = (int) $request->user()->id;

        if ($final !== '') {
            $formulaire->appendChefAnnotationEntry($serviceCode, $userId, 'rejet', $final);
        } elseif ($hasPrior) {
            $formulaire->appendChefAnnotationEntry(
                $serviceCode,
                $userId,
                'rejet',
                'Rejet ; annotations précédentes conservées comme motif.'
            );
        } else {
            return redirect()->back()->withErrors(['commentaire' => 'Veuillez saisir le motif de rejet.']);
        }

        $formulaire->status = Formulaire::STATUS_REJETE;
        $formulaire->last_decision_chef_service_code = trim((string) $formulaire->service_code);
        $formulaire->save();

        event(new DossierMisAJour($formulaire->fresh()));
        $this->notifierSecretaire($formulaire, 'rejete');

        $this->logWorkflow($formulaire, 'chef_formulaire_rejected', [
            'service_code' => $serviceCode,
        ]);

        return redirect()->route('chefService.forms.show', $formulaire)->with('success', 'Dossier rejeté. Le secrétariat a été notifié.');
    }

    private function notifierSecretaire(Formulaire $formulaire, string $type): void
    {
        User::secretairesNotifiablesPourService($formulaire->service_code)
            ->each(function (User $secretaire) use ($formulaire, $type): void {
                $secretaire->notify(new DecisionChefSurDossier($formulaire, $type));
            });
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
