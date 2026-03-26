<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Formulaire réservé à l’admin : créer un compte secrétaire ou chef (identifiants à communiquer hors ligne).
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    //Voir un utilisateur
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:secretaire,chef_de_service'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $newUser = User::create($validated);
        $newUser->syncRoles([$newUser->role]);

        $message = 'Compte créé. Communiquez à la personne son adresse e-mail et le mot de passe que vous avez défini : elle pourra se connecter depuis la page de connexion.';

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message)
            ->with('created_email', $validated['email']);
    }

    //Mettre à jour un utilisateur
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $roleRule = $user->role === 'admin'
            ? ['required', 'in:admin']
            : ['required', 'in:secretaire,chef_de_service'];

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => $roleRule,
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Utilisateur mis à jour');
    }

    //Supprimer un utilisateur
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Le compte administrateur initial ne peut pas être supprimé.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Utilisateur supprimé');
    }
}
