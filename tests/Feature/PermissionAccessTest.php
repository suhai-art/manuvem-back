<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Valida o novo modelo de autorização baseado em permissões granulares
 * (spatie/laravel-permission) aplicadas nas rotas via middleware `can:`.
 *
 * Os papéis e permissões são semeados no setUp (PermissionSeeder), então:
 *  - root         -> permissão curinga "*" (acesso total)
 *  - admin        -> todas as permissões granulares (client.*, item.*, ...)
 *  - user         -> apenas permissões de leitura ("*.view")
 */
class PermissionAccessTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_guest_cannot_access_protected_routes(): void
    {
        $this->getJson($this->baseUrl.'/api/users')
            ->assertUnauthorized();
    }

    public function test_user_without_permissions_is_forbidden(): void
    {
        $user = $this->createUserWithoutRole();
        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl.'/api/users')
            ->assertForbidden();
    }

    public function test_admin_with_permissions_can_access(): void
    {
        $admin = $this->createUserWithRole('admin');
        Sanctum::actingAs($admin);

        $this->getJson($this->baseUrl.'/api/users')->assertOk();
        $this->getJson($this->baseUrl.'/api/clients')->assertOk();
        $this->getJson($this->baseUrl.'/api/items')->assertOk();
    }

    public function test_root_has_access_to_everything(): void
    {
        $root = $this->createUserWithRole('root');
        Sanctum::actingAs($root);

        // Acesso de leitura
        $this->getJson($this->baseUrl.'/api/users')->assertOk();

        // Acesso de escrita: passa pela permissão e cai na validação (422),
        // provando que o middleware de permissão autorizou a requisição.
        $this->postJson($this->baseUrl.'/api/users', [])->assertStatus(422);
    }

    public function test_user_with_view_permission_can_list_but_not_create(): void
    {
        $viewer = $this->createUserWithRole('user'); // apenas *.view
        Sanctum::actingAs($viewer);

        // Leitura permitida (user.view / client.view / item.view)
        $this->getJson($this->baseUrl.'/api/users')->assertOk();
        $this->getJson($this->baseUrl.'/api/clients')->assertOk();
        $this->getJson($this->baseUrl.'/api/items')->assertOk();

        // Escrita negada (sem *_create)
        $this->postJson($this->baseUrl.'/api/users', [])->assertForbidden();
        $this->postJson($this->baseUrl.'/api/clients', [])->assertForbidden();
        $this->deleteJson($this->baseUrl.'/api/items/1')->assertForbidden();
    }

    public function test_permission_management_requires_permissions_manage(): void
    {
        $viewer = $this->createUserWithRole('user'); // não tem permissions.manage
        Sanctum::actingAs($viewer);

        $this->getJson($this->baseUrl.'/api/permissions')->assertForbidden();

        $admin = $this->createUserWithRole('admin'); // tem permissions.manage
        Sanctum::actingAs($admin);

        $this->getJson($this->baseUrl.'/api/permissions')->assertOk();
    }

    public function test_can_create_role_and_assign_existing_permissions(): void
    {
        $admin = $this->createUserWithRole('admin'); // tem permissions.manage
        Sanctum::actingAs($admin);

        // Cria um novo papel
        $roleId = $this->postJson($this->baseUrl.'/api/permissions/roles', ['name' => 'editor'])
            ->assertCreated()
            ->assertJsonFragment(['name' => 'editor'])
            ->json('data.id');

        // Atribui uma permissão já existente ao papel recém-criado
        $this->postJson($this->baseUrl."/api/permissions/roles/{$roleId}/attach", [
            'permission' => 'client.view',
        ])->assertOk();

        // O papel agora possui a permissão
        $this->assertDatabaseHas('role_has_permissions', [
            'role_id' => $roleId,
        ]);
    }

    public function test_cannot_create_role_without_permissions_manage(): void
    {
        $viewer = $this->createUserWithRole('user'); // não tem permissions.manage
        Sanctum::actingAs($viewer);

        $this->postJson($this->baseUrl.'/api/permissions/roles', ['name' => 'hacker'])
            ->assertForbidden();

        $this->assertDatabaseMissing('roles', ['name' => 'hacker']);
    }

    public function test_cannot_assign_nonexistent_permission_to_role(): void
    {
        $admin = $this->createUserWithRole('admin');
        Sanctum::actingAs($admin);
        $adminRoleId = Role::where('name', 'admin')->first()->id;

        // Tenta atribuir uma permissão que não existe (não deve ser criada)
        $this->postJson($this->baseUrl."/api/permissions/roles/{$adminRoleId}/attach", [
            'permission' => 'ghost.action',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('permissions', ['name' => 'ghost.action']);
    }

    public function test_sync_role_ignores_nonexistent_permissions(): void
    {
        $admin = $this->createUserWithRole('admin');
        Sanctum::actingAs($admin);
        $adminRoleId = Role::where('name', 'admin')->first()->id;

        // Sincroniza misturando permissões válidas e inexistentes
        $response = $this->postJson($this->baseUrl."/api/permissions/roles/{$adminRoleId}/sync", [
            'permissions' => ['client.view', 'nonexistent.permission'],
        ])->assertOk();

        // A inexistente é reportada e não é criada
        $response->assertJsonFragment(['missing_permissions' => ['nonexistent.permission']]);
        $this->assertDatabaseMissing('permissions', ['name' => 'nonexistent.permission']);
    }

    public function test_cannot_create_permissions_endpoint_rejects_post(): void
    {
        $admin = $this->createUserWithRole('admin');
        Sanctum::actingAs($admin);

        // O endpoint de criação de permissão foi removido: POST não é permitido.
        $this->postJson($this->baseUrl.'/api/permissions', ['name' => 'new.permission'])
            ->assertStatus(405);

        $this->assertDatabaseMissing('permissions', ['name' => 'new.permission']);
    }
}
