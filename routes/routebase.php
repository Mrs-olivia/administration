<?php
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Secretaire\FormulaireController as SecretaireController;
use App\Http\Controllers\ChefService\FormulaireController as ChefServiceController;

// ------------------ ADMIN ------------------
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');           
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');       
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');          
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');     
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy'); 
});
Route::get('formulaires/{formulaire}/edit', [FormulaireController::class, 'edit'])->name('formulaires.edit');
// ------------------ SECRÉTAIRE ------------------
Route::prefix('secretaire')->middleware('auth')->group(function () {
    Route::get('/forms', [SecretaireController::class, 'index'])->name('secretaire.forms.index');      
    Route::get('/forms/{id}', [SecretaireController::class, 'show'])->name('secretaire.forms.show');   
    Route::post('/forms', [SecretaireController::class, 'store'])->name('secretaire.forms.store');
    Route::get('/forms/{formulaire}/edit', [SecretaireController::class, 'edit'])->name('secretaire.forms.edit');       
    Route::put('/forms/{id}', [SecretaireController::class, 'update'])->name('secretaire.forms.update'); 
});

// ------------------ CHEF DE SERVICE ------------------
Route::prefix('chefService')->middleware('auth')->group(function () {
    Route::get('/forms', [ChefServiceController::class, 'index'])->name('chefService.forms.index');          
    Route::get('/forms/{id}', [ChefServiceController::class, 'show'])->name('chefService.forms.show');  
    Route::post('/forms/{formulaire}/edit', [ChefServiceController::class, 'edit'])->name('chefService.forms.edit');     
    Route::post('/forms/{id}/approve', [ChefServiceController::class, 'approve'])->name('chefService.forms.approve'); 
    Route::post('/forms/{id}/cancel', [ChefServiceController::class, 'cancel'])->name('chefService.forms.cancel');   
    Route::post('/forms/{id}/note', [ChefServiceController::class, 'addNote'])->name('chefService.forms.note');   
});