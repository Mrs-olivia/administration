<?php

use App\Http\Controllers\FormulaireController;
use Illuminate\Support\Facades\Route;

Route::get('secretaire', [FormulaireController::class, 'index'])->name('secretaire');
Route::post('formulaires', [FormulaireController::class, 'store'])->name('formulaires.store');
Route::get('formulaires/{formulaire}', [FormulaireController::class, 'show'])->name('formulaires.show');
Route::get('formulaires/{formulaire}/edit', [FormulaireController::class, 'edit'])->name('formulaires.edit');
Route::put('formulaires/{formulaire}', [FormulaireController::class, 'update'])->name('formulaires.update');
Route::delete('formulaires/{formulaire}', [FormulaireController::class, 'destroy'])->name('formulaires.destroy');
    