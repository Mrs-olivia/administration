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
        return in_array($user->role, ['secretaire', 'chef_de_service'], true);
    }

    public function create(User $user): bool
    {
        return $user->role === 'secretaire';
    }

    public function update(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire' && $formulaire->isEditableBySecretaire();
    }

    public function delete(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire' && $formulaire->isEditableBySecretaire();
    }

    public function send(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire' && $formulaire->canBeSentToChef();
    }

    public function archive(User $user, Formulaire $formulaire): bool
    {
        return $user->role === 'secretaire' && $formulaire->canBeArchivedBySecretaire();
    }

    /** Annoter / valider / rejeter : dossier pas encore clos côté chef. */
    public function agirCommeChef(User $user, Formulaire $formulaire): bool
    {
        if ($user->role !== 'chef_de_service') {
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
