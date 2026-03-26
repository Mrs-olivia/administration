<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        $labels = config('permissions', []);
        foreach (array_keys($labels) as $name) {
            Permission::findOrCreate($name, $guard);
        }

        $admin = Role::findOrCreate('admin', $guard);
        $secretaire = Role::findOrCreate('secretaire', $guard);
        $chef = Role::findOrCreate('chef_de_service', $guard);

        $admin->syncPermissions(Permission::all());

        $secretaire->syncPermissions([
            'dashboard.secretaire',
            'formulaires.view',
            'formulaires.create',
            'formulaires.edit',
            'formulaires.delete',
        ]);

        $chef->syncPermissions([
            'dashboard.chef',
            'formulaires.view',
            'formulaires.annotate',
            'formulaires.approve',
            'formulaires.reject',
        ]);

        User::query()->each(function (User $user): void {
            if ($user->role && Role::where('name', $user->role)->exists()) {
                $user->syncRoles([$user->role]);
            }
        });

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
