<?php

namespace App\Http\Controllers\Secretaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formulaire;
use App\Http\Requests\StoreformulaireRequest;
use App\Http\Requests\UpdateformulaireRequest;

class FormulaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formulaires = Formulaire::latest()->paginate(15);
        return view('secretaire.formulaire.index', compact('formulaires'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreformulaireRequest $request)
    {
        $data = $request->validated();

        // Si "Autre" est choisi, on prend la valeur du champ texte
        if ($request->type_document === 'Autre' && $request->filled('autre_type_document')) {
            $data['type_document'] = $request->autre_type_document;
    }
        
        // // Si un fichier est téléchargé, stockez-le
        // if ($request->hasFile('fichier')) {
        //     $file = $request->file('fichier');
        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $file->storeAs('formulaires', $fileName, 'public');
        //     $data['fichier'] = 'formulaires/' . $fileName;
        // }
        // 1. Gestion du type de document (si "Autre")
    if ($request->type_document === 'Autre' && $request->filled('autre_type_document')) {
        $data['type_document'] = $request->autre_type_document;
    }

    // 2. LE FICHIER : C'est ici que tout se joue
    if ($request->hasFile('fichier')) {
        // On enregistre le fichier REEL dans le dossier 'storage/app/public/documents'
        $path = $request->file('fichier')->store('documents', 'public');
        
        // On remplace l'objet fichier par son CHEMIN (le texte de l'adresse)
        // pour que la base de données puisse l'enregistrer
        $data['fichier'] = $path;
    }
        
        $formulaire = Formulaire::create($data);

        return redirect()->route('secretaire.forms.index')->with('success', 'Formulaire rempli avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Formulaire $formulaire)
    {
        return view('secretaire.formulaire.show', compact('formulaire'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formulaire $formulaire)
    {
        return view('secretaire.formulaire.edit', compact('formulaire'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateformulaireRequest $request, Formulaire $formulaire)
    {
        $data = $request->validated();
        
        // Si un nouveau fichier est téléchargé
        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier s'il existe
            if ($formulaire->fichier) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($formulaire->fichier);
            }
            
            $file = $request->file('fichier');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('formulaires', $fileName, 'public');
            $data['fichier'] = 'formulaires/' . $fileName;
        }
        
        $formulaire->update($data);

        return redirect()->route('secretaire.forms.index')->with('success', 'Formulaire modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formulaire $formulaire)
    {
        // Supprimer le fichier si existant
        if ($formulaire->fichier) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($formulaire->fichier);
        }
        
        $formulaire->delete();
        
        return redirect()->route('secretaire.forms.index')->with('success', 'Formulaire supprimé avec succès.');
    }
}
