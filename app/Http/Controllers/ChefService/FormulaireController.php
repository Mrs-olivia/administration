<?php

namespace App\Http\Controllers\ChefService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formulaire;

class FormulaireController extends Controller
{
    // List all pending forms
    public function index()
    {
    
        $formulaires = Formulaire::whereIn('status', [
            Formulaire::STATUS_EN_ATTENTE,
            Formulaire::STATUS_TRAITE,
            Formulaire::STATUS_REJETE
        ])->latest()->paginate(15);
        return View('chefService.formulaire.index', compact('formulaires'));
    }

    // Show a specific form
    public function show($id)
    {
        $form = Formulaire::findOrFail($id);
        return View('chefService.formulaire.show', compact('form'));
    }

    // Approve a form
    public function approve($id)
    {
        $formulaire = Formulaire::findOrFail($id);
        $formulaire->status = 2;
        $formulaire->save();
        return View('chefService.formulaire.show', compact('formulaire'))->with('success', 'Form approved successfully');
    }

    // Cancel a form
    public function cancel($id)
    {
        $formulaire = Formulaire::findOrFail($id);
        $formulaire->status = 3;
        $formulaire->save();

        return View('chefService.formulaire.show', compact('formulaire'))->with('success', 'Form canceled successfully');
    }

    // Add a note to a form
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string'
        ]);

        $formulaire = Formulaire::findOrFail($id);
        $formulaire->note = $request->note;
        $formulaire->save();

        return View('chefService.formulaire.show', compact('formulaire'))->with('success', 'Note added successfully');
    }
}
