<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_authenticated_user_can_get_his_information(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl . '/api/auth/me')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            ]);
    }

    public function test_guest_cannot_access_me(): void
    {
        $this->getJson($this->baseUrl . '/api/auth/me')
            ->assertUnauthorized();
    }

    public function test_me_includes_tenant_name(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl . '/api/auth/me')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['tenant'],
            ]);
    }

    public function test_me_includes_roles_and_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson($this->baseUrl . '/api/auth/me')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'status',
                    'roles',
                    'permissions',
                    'tenant',
                ],
            ])
            ->assertJson([
                'data' => [
                    'roles' => ['admin'],
                ],
            ]);

        // admin must carry every granular permission seeded by PermissionSeeder
        $permissions = $this->getJson($this->baseUrl . '/api/auth/me')
            ->json('data.permissions');

        $this->assertNotEmpty($permissions);
        $this->assertContains('client.view', $permissions);
    }

    public function test_me_permissions_are_empty_for_user_without_role(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl . '/api/auth/me')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'roles' => [],
                    'permissions' => [],
                ],
            ]);
    }
}
