<?php

namespace Tests\Unit\Actions\Item;

use App\Actions\Item\DeleteItemAction;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteItemActionTest extends TestCase
{
    protected bool $tenancy = true;

    private DeleteItemAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new DeleteItemAction();
    }

    public function test_execute_soft_deletes_the_item(): void
    {
        $item = Item::factory()->create();

        $this->action->execute($item->id);

        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    public function test_execute_removes_item_from_default_query_results(): void
    {
        $item = Item::factory()->create();

        $this->action->execute($item->id);

        $this->assertNull(Item::query()->find($item->id));
    }

    public function test_execute_throws_exception_when_item_does_not_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->action->execute((string) Str::uuid());
    }

    public function test_execute_throws_exception_when_item_is_already_deleted(): void
    {
        $item = Item::factory()->create();
        $item->delete();

        $this->expectException(ModelNotFoundException::class);

        $this->action->execute($item->id);
    }
}
