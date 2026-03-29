<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_user_cannot_be_promoted_to_admin_via_update(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'secretaire', 'service_code' => 'SVC1']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $staff->id), [
                'name' => $staff->name,
                'email' => $staff->email,
                'role' => 'admin',
                'service_code' => 'SVC1',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'role' => 'secretaire',
        ]);
    }

    public function test_initial_admin_account_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin->id))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('modal_error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
