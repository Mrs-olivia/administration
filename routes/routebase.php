<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Secretaire\FormulaireController as SecretaireController;
use App\Http\Controllers\ChefService\FormulaireController as ChefServiceController;

// ------------------ ADMIN ------------------
Route::prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');           
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');       
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');          
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');     
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy'); 
});

// ------------------ SECRÉTAIRE ------------------
Route::prefix('secretaire')->group(function () {
    Route::get('/forms', [SecretaireController::class, 'index'])->name('secretaire.forms.index');      
    Route::get('/forms/{id}', [SecretaireController::class, 'show'])->name('secretaire.forms.show');   
    Route::post('/forms', [SecretaireController::class, 'store'])->name('secretaire.forms.store');      
    Route::put('/forms/{id}', [SecretaireController::class, 'update'])->name('secretaire.forms.update'); 
});

// ------------------ CHEF DE SERVICE ------------------
Route::prefix('chefService')->group(function () {
    Route::get('/forms', [ChefServiceController::class, 'index'])->name('chef.forms.index');          
    Route::get('/forms/{id}', [ChefServiceController::class, 'show'])->name('chef.forms.show');     
    Route::post('/forms/{id}/approve', [ChefServiceController::class, 'approve'])->name('chef.forms.approve'); 
    Route::post('/forms/{id}/cancel', [ChefServiceController::class, 'cancel'])->name('chef.forms.cancel');   
    Route::post('/forms/{id}/note', [ChefServiceController::class, 'addNote'])->name('chef.forms.note');   
});