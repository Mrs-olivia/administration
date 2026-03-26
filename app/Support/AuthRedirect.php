<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;

class AuthRedirect
{
    /**
     * URL d’accueil après connexion ou inscription selon le rôle applicatif (colonne users.role).
     */
    public static function homeUrl(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.permissions.index'),
            'secretaire' => route('secretaire.dashboard'),
            'chef_de_service' => route('chefService.dashboard'),
            default => url('/dashboard'),
        };
    }

    /**
     * Évite de renvoyer un non-admin vers une URL /admin/* (ex. session « intended » laissée par un admin).
     */
    public static function userMayAccessUrl(User $user, string $absoluteOrRelativeUrl): bool
    {
        $path = parse_url($absoluteOrRelativeUrl, PHP_URL_PATH);
        if ($path === null || $path === '') {
            $path = Str::start($absoluteOrRelativeUrl, '/');
        }

        if (str_starts_with($path, '/admin')) {
            return $user->role === 'admin';
        }

        return true;
    }

    /**
     * Cible après login : intended si autorisé pour ce rôle, sinon tableau de bord du rôle.
     */
    public static function afterLoginUrl(User $user, ?string $intendedUrl): string
    {
        if ($intendedUrl !== null && $intendedUrl !== '' && self::userMayAccessUrl($user, $intendedUrl)) {
            return $intendedUrl;
        }

        return self::homeUrl($user);
    }
}
