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
        $forms = Formulaire::where('status', 'pending')->get();
        return View('chefService.formulaire.index', compact('forms'));
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
        $form = Formulaire::findOrFail($id);
        $form->status = 'approved';
        $form->save();
        return View('chefService.formulaire.show', compact('form'))->with('success', 'Form approved successfully');
    }

    // Cancel a form
    public function cancel($id)
    {
        $form = Formulaire::findOrFail($id);
        $form->status = 'canceled';
        $form->save();

        return View('chefService.formulaire.show', compact('form'))->with('success', 'Form canceled successfully');
    }

    // Add a note to a form
    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string'
        ]);

        $form = Formulaire::findOrFail($id);
        $form->note = $request->note;
        $form->save();

        return View('chefService.formulaire.show', compact('form'))->with('success', 'Note added successfully');
    }
}
