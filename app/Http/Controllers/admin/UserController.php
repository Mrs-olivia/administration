<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

    // Voir un utilisateur
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $serviceCodes = array_keys(config('administration.services', []));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:secretaire,chef_de_service'],
            'service_code' => [
                Rule::requiredIf($request->input('role') === 'chef_de_service'),
                'nullable',
                'string',
                'max:10',
                Rule::in($serviceCodes),
            ],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        if (($validated['service_code'] ?? null) === '') {
            $validated['service_code'] = null;
        }

        $newUser = User::create($validated);
        $newUser->syncRoles([$newUser->role]);

        $message = 'Compte créé. Communiquez à la personne son adresse e-mail et le mot de passe que vous avez défini : elle pourra se connecter depuis la page de connexion.';

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message)
            ->with('created_email', $validated['email']);
    }

    // Mettre à jour un utilisateur
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $roleRule = $user->role === 'admin'
            ? ['required', 'in:admin']
            : ['required', 'in:secretaire,chef_de_service'];

        $serviceCodes = array_keys(config('administration.services', []));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => $roleRule,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'service_code' => [
                Rule::requiredIf($request->input('role') === 'chef_de_service'),
                'nullable',
                'string',
                'max:10',
                Rule::in($serviceCodes),
            ],
        ]);

        if ($request->filled('password')) {
            $rules['service_code'] = ['required', 'string', 'max:10', Rule::in($serviceCodes)];
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            if (User::isPasswordUsedByAnotherUser($validated['password'], (int) $user->id)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['password' => 'Ce mot de passe est déjà utilisé par un autre utilisateur. Choisissez-en un autre.']);
            }
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($user->role === 'admin') {
            unset($validated['service_code']);
        } elseif (($validated['service_code'] ?? null) === '') {
            $validated['service_code'] = null;
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Utilisateur mis à jour');
    }

    // Supprimer un utilisateur
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
