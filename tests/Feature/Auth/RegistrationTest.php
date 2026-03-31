<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_register_route_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_guest_cannot_post_register(): void
    {
        $this->post('/register', [
            'name' => 'X',
            'email' => 'x@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'secretaire',
        ])->assertNotFound();
    }

    public function test_admin_can_create_staff_user_via_admin_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Nouveau Secrétaire',
                'email' => 'nouveau.secretaire@example.com',
                'password' => 'UniquePass123!',
                'password_confirmation' => 'UniquePass123!',
                'role' => 'secretaire',
                'service_code' => 'SVC1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'nouveau.secretaire@example.com',
            'role' => 'secretaire',
            'service_code' => 'SVC1',
        ]);
    }
}
