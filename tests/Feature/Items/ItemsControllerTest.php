<?php

namespace Tests\Feature\Items;

use App\Models\Item;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemsControllerTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_authenticated_admin_can_list_items(): void
    {
        Item::factory()->count(3)->create();
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson($this->baseUrl . '/api/items');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_guest_cannot_list_items(): void
    {
        $this->getJson($this->baseUrl . '/api/items')
            ->assertUnauthorized();
    }

    public function test_non_admin_cannot_list_items(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl . '/api/items')
            ->assertForbidden();
    }

    public function test_authenticated_admin_can_find_one_item(): void
    {
        $item = Item::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson($this->baseUrl . '/api/items/' . $item->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                ],
            ]);
    }

    public function test_authenticated_admin_can_create_an_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson($this->baseUrl . '/api/items', [
            'internal_code' => 'ITEM-001',
            'name' => 'Test Item',
            'description' => 'A test item description',
            'default_unit_price' => 99.99,
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('items', [
            'internal_code' => 'ITEM-001',
            'name' => 'Test Item',
        ]);
    }

    public function test_authenticated_admin_can_update_an_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $item = Item::factory()->create();

        $response = $this->putJson($this->baseUrl . '/api/items/' . $item->id, [
            'internal_code' => 'ITEM-UPDATED',
            'name' => 'Updated Item',
            'description' => 'Updated description',
            'default_unit_price' => 149.99,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'internal_code' => 'ITEM-UPDATED',
            'name' => 'Updated Item',
        ]);
    }

    public function test_authenticated_admin_can_delete_an_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);
        $item = Item::factory()->create();

        $response = $this->deleteJson($this->baseUrl . '/api/items/' . $item->id);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['message' => 'Item removido com sucesso.'],
            ]);

        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    public function test_create_item_requires_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson($this->baseUrl . '/api/items', []);

        $response->assertStatus(422);
    }

    public function test_create_item_requires_valid_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson($this->baseUrl . '/api/items', [
            'internal_code' => 'ITEM-001',
            'name' => 'Test Item',
            'description' => 'Description',
            'default_unit_price' => -10,
        ]);

        $response->assertStatus(422);
    }
}
