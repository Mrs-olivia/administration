<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'user.role' => \App\Http\Middleware\RoleMiddleware::class,
            'staff.service' => \App\Http\Middleware\EnsureStaffHasService::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
         * Les refus d'autorisation (Gate, policies, Spatie, abort(403)) deviennent en pratique
         * des HttpException 403 après prepareException() — une seule branche suffit.
         */
        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 403) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => config('messages.action_denied'),
                ], 403);
            }

            return redirect()
                ->back(fallback: '/')
                ->with('modal_error', true);
        });
    })->create();
