<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // Seul compte administrateur : créé ici (ou équivalent en dur). Aucun autre admin via l’UI.
        $admin = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // WithoutModelEvents sur ce seeder empêche l’événement saved → syncRoles ne tourne pas.
        // Spatie (middleware role:admin) lit model_has_roles, pas la colonne users.role.
        $admin->syncRoles([$admin->role]);
    }
}
