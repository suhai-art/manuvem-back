<?php

namespace Tests\Unit\Actions\Item;

use App\Actions\Item\CreateUpdateItemAction;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateUpdateItemActionTest extends TestCase
{
    protected bool $tenancy = true;

    private CreateUpdateItemAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreateUpdateItemAction();
    }

    public function test_execute_creates_a_new_item_when_no_id_is_given(): void
    {
        $data = [
            'internal_code' => 'ITEM-0001',
            'name' => 'Cadeira de escritório',
            'description' => 'Cadeira ergonômica com apoio de braço.',
            'default_unit_price' => 199.90,
        ];

        $item = $this->action->execute($data);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'internal_code' => 'ITEM-0001',
            'name' => 'Cadeira de escritório',
        ]);
    }

    public function test_execute_returns_an_item_instance_with_the_given_data(): void
    {
        $data = [
            'internal_code' => 'ITEM-0002',
            'name' => 'Mesa de reunião',
            'description' => 'Mesa para até 8 pessoas.',
            'default_unit_price' => 899.00,
        ];

        $item = $this->action->execute($data);

        $this->assertEquals('ITEM-0002', $item->internal_code);
        $this->assertEquals('Mesa de reunião', $item->name);
        $this->assertEquals(899.00, (float) $item->default_unit_price);
    }

    public function test_execute_updates_an_existing_item_when_id_is_given(): void
    {
        $item = Item::factory()->create([
            'name' => 'Nome antigo',
        ]);

        $updated = $this->action->execute([
            'internal_code' => $item->internal_code,
            'name' => 'Nome atualizado',
            'description' => $item->description,
            'default_unit_price' => $item->default_unit_price,
        ], $item->id);

        $this->assertTrue($updated->is($item));
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Nome atualizado',
        ]);
    }

    public function test_execute_does_not_create_a_new_row_when_updating(): void
    {
        $item = Item::factory()->create();

        $this->action->execute([
            'internal_code' => $item->internal_code,
            'name' => 'Novo nome',
            'description' => $item->description,
            'default_unit_price' => $item->default_unit_price,
        ], $item->id);

        $this->assertDatabaseCount('items', 1);
    }

    public function test_execute_throws_exception_when_updating_a_nonexistent_item(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->action->execute([
            'internal_code' => 'ITEM-9999',
            'name' => 'Não existe',
            'description' => 'Descrição',
            'default_unit_price' => 10,
        ], (string) Str::uuid());
    }
}
