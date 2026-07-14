<?php

namespace Tests\Unit\Actions\Item;

use App\Actions\Item\FindItemAction;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindItemActionTest extends TestCase
{
    protected bool $tenancy = true;

    private FindItemAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new FindItemAction();
    }

    public function test_find_returns_paginated_items(): void
    {
        Item::factory()->count(20)->create();

        $result = $this->action->find(1, '', 15);

        $this->assertEquals(20, $result->total());
        $this->assertEquals(15, $result->perPage());
        $this->assertCount(15, $result->items());
    }

    public function test_find_returns_second_page(): void
    {
        Item::factory()->count(20)->create();

        $result = $this->action->find(2, '', 15);

        $this->assertEquals(2, $result->currentPage());
        $this->assertCount(5, $result->items());
    }

    public function test_find_filters_by_name(): void
    {
        Item::factory()->create(['name' => 'Cadeira de escritório']);
        Item::factory()->create(['name' => 'Mesa de reunião']);

        $result = $this->action->find(1, 'Cadeira', 15);

        $this->assertCount(1, $result->items());
        $this->assertEquals('Cadeira de escritório', $result->items()[0]->name);
    }

    public function test_find_filters_by_internal_code(): void
    {
        Item::factory()->create(['internal_code' => 'ITEM-0001']);
        Item::factory()->create(['internal_code' => 'ITEM-0002']);

        $result = $this->action->find(1, 'ITEM-0001', 15);

        $this->assertCount(1, $result->items());
        $this->assertEquals('ITEM-0001', $result->items()[0]->internal_code);
    }

    public function test_find_returns_all_items_when_search_is_empty(): void
    {
        Item::factory()->count(3)->create();

        $result = $this->action->find(1, '', 15);

        $this->assertEquals(3, $result->total());
    }

    public function test_find_one_returns_the_matching_item(): void
    {
        $item = Item::factory()->create();

        $result = $this->action->findOne($item->id);

        $this->assertTrue($result->is($item));
    }

    public function test_find_one_throws_exception_when_item_does_not_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->action->findOne((string) Str::uuid());
    }

    public function test_find_one_does_not_return_soft_deleted_items(): void
    {
        $item = Item::factory()->create();
        $item->delete();

        $this->expectException(ModelNotFoundException::class);

        $this->action->findOne($item->id);
    }
}
