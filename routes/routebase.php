<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ChefService\DashboardController as ChefServiceDashboardController;
use App\Http\Controllers\ChefService\FormulaireController as ChefServiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Secretaire\DashboardController as SecretaireDashboardController;
use App\Http\Controllers\Secretaire\FormulaireController as SecretaireController;
use Illuminate\Support\Facades\Route;

// ------------------ ADMIN ------------------
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('role.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('role.store');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('role.destroy');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::put('/permissions', [PermissionController::class, 'update'])->name('admin.permissions.update');
});

// ------------------ SECRÉTAIRE ------------------
Route::prefix('secretaire')->middleware('auth')->group(function () {
    Route::get('/dashboard', [SecretaireDashboardController::class, 'index'])
        ->middleware('permission:dashboard.secretaire')
        ->name('secretaire.dashboard');

    Route::get('/forms/poll', [SecretaireController::class, 'poll'])
        ->middleware('permission:formulaires.view')
        ->name('secretaire.forms.poll');
    Route::get('/forms/{formulaire}/poll', [SecretaireController::class, 'pollShow'])
        ->middleware('permission:formulaires.view')
        ->name('secretaire.forms.pollShow');

    Route::get('/forms', [SecretaireController::class, 'index'])
        ->middleware('permission:formulaires.view')
        ->name('secretaire.forms.index');
    Route::post('/forms', [SecretaireController::class, 'store'])
        ->middleware('permission:formulaires.create')
        ->name('secretaire.forms.store');
    Route::get('/forms/{formulaire}', [SecretaireController::class, 'show'])
        ->middleware('permission:formulaires.view')
        ->name('secretaire.forms.show');
    Route::get('/forms/{formulaire}/edit', [SecretaireController::class, 'edit'])
        ->middleware('permission:formulaires.edit')
        ->name('secretaire.forms.edit');
    Route::put('/forms/{formulaire}', [SecretaireController::class, 'update'])
        ->middleware('permission:formulaires.edit')
        ->name('secretaire.forms.update');
    Route::delete('/forms/{formulaire}', [SecretaireController::class, 'destroy'])
        ->middleware('permission:formulaires.delete')
        ->name('secretaire.forms.destroy');
    Route::post('/forms/{formulaire}/send', [SecretaireController::class, 'send'])
        ->middleware('permission:formulaires.edit')
        ->name('secretaire.forms.send');
    Route::post('/forms/{formulaire}/archive', [SecretaireController::class, 'archive'])
        ->middleware('permission:formulaires.edit')
        ->name('secretaire.forms.archive');
});

// ------------------ CHEF DE SERVICE ------------------
Route::prefix('chefService')->middleware('auth')->group(function () {
    Route::get('/dashboard', [ChefServiceDashboardController::class, 'index'])
        ->middleware('permission:dashboard.chef')
        ->name('chefService.dashboard');

    Route::get('/forms', [ChefServiceController::class, 'index'])
        ->middleware('permission:formulaires.view')
        ->name('chefService.forms.index');
    Route::get('/forms/{formulaire}', [ChefServiceController::class, 'show'])
        ->middleware('permission:formulaires.view')
        ->name('chefService.forms.show');
    Route::post('/forms/{formulaire}/annoter', [ChefServiceController::class, 'annoter'])
        ->middleware('permission:formulaires.annotate')
        ->name('chefService.forms.annoter');
    Route::post('/forms/{formulaire}/valider', [ChefServiceController::class, 'valider'])
        ->middleware('permission:formulaires.approve')
        ->name('chefService.forms.valider');
    Route::post('/forms/{formulaire}/rejeter', [ChefServiceController::class, 'rejeter'])
        ->middleware('permission:formulaires.reject')
        ->name('chefService.forms.rejeter');
});
