<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(): View
    {
        $labels = config('permissions', []);
        $roles = Role::query()->orderBy('name')->get();
        $permissions = Permission::query()->orderBy('name')->get();

        return view('admin.permissions.index', compact('labels', 'roles', 'permissions'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'matrix' => ['nullable', 'array'],
            'matrix.*' => ['array'],
            'matrix.*.*' => ['integer', 'exists:permissions,id'],
        ]);

        // (Re)calcule la matrice complète, y compris pour le rôle "admin".
        foreach (Role::query()->get() as $role) {
            $ids = $request->input('matrix.'.$role->id, []);
            $role->syncPermissions($ids);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permissions enregistrées.');
    }
}
