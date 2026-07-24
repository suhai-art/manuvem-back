<?php

namespace Tests\Feature\Roles;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesControllerTest extends TestCase
{
    protected bool $tenancy = true;

    private function permissionIds(array $names): array
    {
        return Permission::query()->whereIn('name', $names)->pluck('id')->all();
    }

    public function test_guest_cannot_access_roles(): void
    {
        $this->getJson($this->baseUrl.'/api/roles')->assertUnauthorized();
    }

    public function test_user_without_permissions_is_forbidden(): void
    {
        $viewer = $this->createUserWithRole('user');
        $this->actingAsSanctum($viewer);

        $this->getJson($this->baseUrl.'/api/roles')->assertForbidden();
    }

    public function test_admin_can_list_roles_with_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $response = $this->getJson($this->baseUrl.'/api/roles');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);

        $first = $response->json('data.0');
        $this->assertArrayHasKey('permissions', $first);
    }

    public function test_admin_can_find_a_single_role_with_its_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $role = Role::where('name', 'admin')->firstOrFail();

        $this->getJson($this->baseUrl.'/api/roles/'.$role->id)
            ->assertOk()
            ->assertJsonFragment(['id' => $role->id, 'name' => 'admin'])
            ->assertJsonStructure(['data' => ['permissions' => ['0' => ['id', 'name']]]]);
    }

    public function test_admin_can_create_role_with_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $permissionIds = $this->permissionIds(['client.view', 'client.create']);

        $response = $this->postJson($this->baseUrl.'/api/roles', [
            'name' => 'editor',
            'permission' => $permissionIds,
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'editor'])
            ->assertJsonCount(2, 'data.permissions');

        $role = Role::where('name', 'editor')->firstOrFail();
        $this->assertSame(
            ['client.view', 'client.create'],
            $role->permissions->pluck('name')->sort()->values()->all()
        );
    }

    public function test_creating_role_requires_unique_name(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $this->postJson($this->baseUrl.'/api/roles', ['name' => 'admin'])
            ->assertStatus(422);
    }

    public function test_admin_can_update_role_and_edit_its_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $role = Role::create(['name' => 'temp', 'guard_name' => 'sanctum']);
        $role->syncPermissions($this->permissionIds(['client.view']));

        // Send a new permission set (replace the old one) + rename.
        $response = $this->putJson($this->baseUrl.'/api/roles/'.$role->id, [
            'name' => 'renamed',
            'permission' => $this->permissionIds(['item.view', 'item.update']),
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'renamed'])
            ->assertJsonCount(2, 'data.permissions');

        $role->refresh();
        $this->assertSame(
            ['item.update', 'item.view'],
            $role->permissions->pluck('name')->sort()->values()->all()
        );
    }

    public function test_updating_role_without_permission_key_keeps_existing_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $role = Role::create(['name' => 'keepme', 'guard_name' => 'sanctum']);
        $role->syncPermissions($this->permissionIds(['user.view']));

        $this->putJson($this->baseUrl.'/api/roles/'.$role->id, [
            'name' => 'keepme',
        ])->assertOk();

        $role->refresh();
        $this->assertSame(['user.view'], $role->permissions->pluck('name')->all());
    }

    public function test_cannot_assign_nonexistent_permission_ids(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $response = $this->postJson($this->baseUrl.'/api/roles', [
            'name' => 'ghost',
            'permission' => [999999],
        ]);

        $response->assertCreated();

        $role = Role::where('name', 'ghost')->firstOrFail();
        $this->assertCount(0, $role->permissions);
    }

    public function test_viewer_cannot_create_or_delete_roles(): void
    {
        $viewer = $this->createUserWithRole('user');
        $this->actingAsSanctum($viewer);

        $this->postJson($this->baseUrl.'/api/roles', ['name' => 'nope'])
            ->assertForbidden();

        $role = Role::create(['name' => 'deleteme', 'guard_name' => 'sanctum']);
        $this->deleteJson($this->baseUrl.'/api/roles/'.$role->id)
            ->assertForbidden();
    }

    public function test_admin_can_delete_role_without_users(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        $role = Role::create(['name' => 'deletable', 'guard_name' => 'sanctum']);

        $this->deleteJson($this->baseUrl.'/api/roles/'.$role->id)
            ->assertOk()
            ->assertJsonFragment(['message' => 'Papel removido com sucesso.']);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_cannot_delete_role_still_assigned_to_users(): void
    {
        $admin = $this->createUserWithRole('admin');
        $this->actingAsSanctum($admin);

        // 'admin' role is assigned to the acting user.
        $role = Role::where('name', 'admin')->firstOrFail();

        $this->deleteJson($this->baseUrl.'/api/roles/'.$role->id)
            ->assertStatus(409);

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    private function actingAsSanctum($user)
    {
        \Laravel\Sanctum\Sanctum::actingAs($user);

        return $this;
    }
}
