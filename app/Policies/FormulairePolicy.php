<?php

namespace App\Policies;

use App\Models\Formulaire;
use App\Models\User;

class FormulairePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['secretaire', 'chef_de_service'], true);
    }

    public function view(User $user, Formulaire $formulaire): bool
    {
        if (! in_array($user->role, ['secretaire', 'chef_de_service'], true)) {
            return false;
        }

        if (! $user->sameServiceAsFormulaire($formulaire)) {
            return false;
        }

        // Dossiers saisis par le secrétariat : visibles par le chef seulement après « Envoyer au chef ».
        // Dossiers créés par le chef (envoi secrétariat) : visibles sans cette étape.
        if ($user->role === 'chef_de_service' && $formulaire->sent_to_chef_at === null) {
            if ((int) $formulaire->created_by_user_id !== (int) $user->id) {
                return false;
            }
        }

        return true;
    }

    public function create(User $user): bool
    {
        if (! in_array($user->role, ['secretaire', 'chef_de_service'], true)) {
            return false;
        }

        if ($user->role === 'chef_de_service' && $user->hasUnscopedServiceAccess()) {
            return false;
        }

        return true;
    }

    public function update(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire'
            && $user->sameServiceAsFormulaire($formulaire)
            && $formulaire->isHeldByInitiatingService()
            && ($formulaire->createdBy?->role === 'secretaire')
            && $formulaire->isEditableBySecretaire();
    }

    public function delete(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire'
            && $user->sameServiceAsFormulaire($formulaire)
            && $formulaire->isHeldByInitiatingService()
            && ($formulaire->createdBy?->role === 'secretaire')
            && $formulaire->isEditableBySecretaire();
    }

    public function send(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire'
            && $user->sameServiceAsFormulaire($formulaire)
            && $formulaire->canBeSentToChef()
            && in_array($formulaire->createdBy?->role, ['secretaire', 'chef_de_service'], true);
    }

    public function transfer(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire'
            && $user->sameServiceAsFormulaire($formulaire)
            && $formulaire->canBeTransferredToOtherService();
    }

    /**
     * Archivage : uniquement après décision chef (traité ou rejeté), pour tout dossier du service.
     * (Plus d’archivage « immédiat » uniquement parce que le créateur est le chef.)
     */
    public function archive(User $user, Formulaire $formulaire): bool
    {
        if ($user->role !== 'secretaire' || ! $user->sameServiceAsFormulaire($formulaire)) {
            return false;
        }

        return $formulaire->canBeArchivedBySecretaire();
    }

    /** Annoter / valider / rejeter : dossier pas encore clos côté chef. */
    public function agirCommeChef(User $user, Formulaire $formulaire): bool
    {
        if ($user->role !== 'chef_de_service') {
            return false;
        }

        if (! $user->sameServiceAsFormulaire($formulaire)) {
            return false;
        }

        if ($formulaire->sent_to_chef_at === null) {
            return false;
        }

        return in_array($formulaire->status, [
            Formulaire::STATUS_EN_ATTENTE,
            Formulaire::STATUS_EN_COURS,
        ], true);
    }

    public function restore(User $user, Formulaire $formulaire): bool
    {
        return false;
    }

    public function forceDelete(User $user, Formulaire $formulaire): bool
    {
        return false;
    }
}
