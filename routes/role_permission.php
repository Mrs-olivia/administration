<?php
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;



// Toutes les routes ici seront préfixées par /admin et protégées par 'auth' et 'role:admin'
Route::get('/role', [RoleController::class, 'index'])->name('role.index');
Route::post('/role', [RoleController::class, 'store'])->name('role.store');
Route::delete('/role/{id}', [RoleController::class, 'destroy'])->name('role.destroy');

