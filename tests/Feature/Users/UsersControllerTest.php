<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_authenticated_user_can_list_users(): void
    {
        User::factory()->count(3)->create();
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        $response = $this->getJson($this->baseUrl . '/api/users');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_guest_cannot_list_users(): void
    {
        $this->getJson($this->baseUrl . '/api/users')
            ->assertUnauthorized();
    }

    public function test_non_admin_cannot_list_users(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl . '/api/users')
            ->assertForbidden();
    }

    public function test_authenticated_admin_can_find_one_user(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson($this->baseUrl . '/api/users/' . $user->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_authenticated_admin_can_create_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson($this->baseUrl . '/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
            'role' => 'admin',
        ]);
    }

    public function test_authenticated_admin_can_update_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $user = User::factory()->create();

        $response = $this->putJson($this->baseUrl . '/api/users/' . $user->id, [
            'name' => 'Updated Name',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
            'status' => 'inactive',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => 'admin',
            'status' => 'inactive',
        ]);
    }

    public function test_authenticated_admin_can_delete_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $user = User::factory()->create();

        $response = $this->deleteJson($this->baseUrl . '/api/users/' . $user->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['message' => 'Usuário removido com sucesso.'],
            ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_authenticated_admin_can_toggle_user_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->putJson($this->baseUrl . '/api/users/' . $user->id . '/toggle-active');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['status' => 'inactive'],
            ]);
    }

    public function test_create_user_requires_name_email_and_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson($this->baseUrl . '/api/users', []);

        $response->assertStatus(422);
    }

    public function test_create_user_requires_unique_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $existing = User::factory()->create();

        $response = $this->postJson($this->baseUrl . '/api/users', [
            'name' => 'New User',
            'email' => $existing->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }
}
