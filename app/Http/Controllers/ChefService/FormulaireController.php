<?php

namespace App\Http\Controllers\ChefService;

use App\Events\DossierMisAJour;
use App\Http\Controllers\Controller;
use App\Models\Formulaire;
use App\Models\User;
use App\Notifications\DecisionChefSurDossier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FormulaireController extends Controller
{
    public function index(): View
    {
        $formulaires = Formulaire::query()
            ->where('status', '!=', Formulaire::STATUS_ARCHIVE)
            ->latest()
            ->paginate(15);

        return view('chefService.formulaire.index', compact('formulaires'));
    }

    public function show(Formulaire $formulaire): View
    {
        $this->authorize('view', $formulaire);

        if ($formulaire->status === Formulaire::STATUS_EN_ATTENTE) {
            $formulaire->status = Formulaire::STATUS_EN_COURS;
            $formulaire->save();
            event(new DossierMisAJour($formulaire->fresh()));
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
            $formulaire->annotation_chef = $texte;
            $formulaire->save();
            event(new DossierMisAJour($formulaire->fresh()));

            return redirect()->route('chefService.forms.show', $formulaire)->with('success', 'Annotation enregistrée.');
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
        if ($final !== '') {
            $formulaire->annotation_chef = $final;
        } elseif (! $hasPrior) {
            return redirect()->back()->withErrors(['commentaire' => 'Veuillez saisir un commentaire.']);
        }

        $formulaire->status = Formulaire::STATUS_TRAITE;
        $formulaire->save();

        event(new DossierMisAJour($formulaire->fresh()));
        $this->notifierSecretaire($formulaire, 'valide');

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
        if ($final !== '') {
            $formulaire->annotation_chef = $final;
        } elseif (! $hasPrior) {
            return redirect()->back()->withErrors(['commentaire' => 'Veuillez saisir le motif de rejet.']);
        }

        $formulaire->status = Formulaire::STATUS_REJETE;
        $formulaire->save();

        event(new DossierMisAJour($formulaire->fresh()));
        $this->notifierSecretaire($formulaire, 'rejete');

        return redirect()->route('chefService.forms.show', $formulaire)->with('success', 'Dossier rejeté. Le secrétariat a été notifié.');
    }

    private function notifierSecretaire(Formulaire $formulaire, string $type): void
    {
        if (! $formulaire->created_by_user_id) {
            return;
        }

        $auteur = User::find($formulaire->created_by_user_id);
        $auteur?->notify(new DecisionChefSurDossier($formulaire, $type));
    }
}
