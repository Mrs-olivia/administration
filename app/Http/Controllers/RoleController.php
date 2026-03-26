<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
        /**
        * Display a listing of the resource.
        */
        public function index()
        {
            $roles = Role::all();
            return view('role.index', compact('roles'));
    
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
        public function store(Request $request)
        {
            $request->validate(['name' => 'required|unique:roles,name']);
        
            Role::create(['name' => $request->name]);
        
            return redirect()->back()->with('success', 'Rôle créé avec succès.');
        }
    
        /**
        * Display the specified resource.
        */
        public function show(string $id)
        {
            //
        }
    
        /**
        * Show the form for editing the specified resource.
        */
        public function edit(string $id)
        {
            //
        }
    
        /**
        * Update the specified resource in storage.
        */
        public function update(Request $request, string $id)
        {
            //
        }
    
        /**
        * Remove the specified resource from storage.
        */
        public function destroy(string $id)
        {
            $role = Role::findById($id);

            if (in_array($role->name, ['admin', 'secretaire', 'chef_de_service'], true)) {
                return redirect()->back()->with('error', 'Ce rôle système ne peut pas être supprimé.');
            }

            $role->delete();

            return redirect()->back()->with('success', 'Rôle supprimé avec succès.');
        }
}
