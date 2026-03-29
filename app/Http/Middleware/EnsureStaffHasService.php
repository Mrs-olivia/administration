<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffHasService
{
    /**
     * Secrétaire et chef : service obligatoire. L’administrateur n’emprunte pas ces routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && in_array($user->role, ['secretaire', 'chef_de_service'], true) && blank($user->service_code)) {
            abort(403, 'Votre compte doit être rattaché à un service. Contactez l’administrateur.');
        }

        return $next($request);
    }
}
