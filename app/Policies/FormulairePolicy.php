<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Formulaire;

/*
 Note: All policy methods currently return `false`, which means every
 authorization check using this policy will be denied (HTTP 403).
 This is likely the cause of access problems when hitting routes that
 rely on these policies. For testing, either return `true` here or
 implement proper permission logic based on the `User`.
*/

class FormulairePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Formulaire $formulaire): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Formulaire $formulaire): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Formulaire $formulaire): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Formulaire $formulaire): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Formulaire $formulaire): bool
    {
        return false;
    }
}
