<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientsControllerTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_authenticated_admin_can_list_clients(): void
    {
        Client::factory()->count(3)->create();
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson($this->baseUrl . '/api/clients');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_guest_cannot_list_clients(): void
    {
        $this->getJson($this->baseUrl . '/api/clients')
            ->assertUnauthorized();
    }

    public function test_non_admin_cannot_list_clients(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl . '/api/clients')
            ->assertForbidden();
    }

    public function test_authenticated_admin_can_find_one_client(): void
    {
        $client = Client::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson($this->baseUrl . '/api/clients/' . $client->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $client->id,
                    'name' => $client->name,
                ],
            ]);
    }

    public function test_authenticated_admin_can_create_a_client(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson($this->baseUrl . '/api/clients', [
            'name' => 'Test Client',
            'document' => '12345678901',
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client',
            'document' => '12345678901',
        ]);
    }

    public function test_authenticated_admin_can_update_a_client(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $client = Client::factory()->create();

        $response = $this->putJson($this->baseUrl . '/api/clients/' . $client->id, [
            'name' => 'Updated Client',
            'document' => '98765432109',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Client',
            'document' => '98765432109',
        ]);
    }

    public function test_authenticated_admin_can_delete_a_client(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $client = Client::factory()->create();

        $response = $this->deleteJson($this->baseUrl . '/api/clients/' . $client->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['message' => 'Client removido com sucesso.'],
            ]);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_create_client_requires_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson($this->baseUrl . '/api/clients', []);

        $response->assertStatus(422);
    }
}
