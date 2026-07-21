<?php

namespace Tests\Unit\Actions\User;

use App\Actions\User\FindUserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class FindUserActionTest extends TestCase
{
    protected bool $tenancy = true;

    private FindUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new FindUserAction();
    }

    public function test_find_returns_paginated_users(): void
    {
        User::factory()->count(20)->create();

        $result = $this->action->find(1, '', 15);

        $this->assertEquals(20, $result->total());
        $this->assertEquals(15, $result->perPage());
        $this->assertCount(15, $result->items());
    }

    public function test_find_returns_second_page(): void
    {
        User::factory()->count(20)->create();

        $result = $this->action->find(2, '', 15);

        $this->assertEquals(2, $result->currentPage());
        $this->assertCount(5, $result->items());
    }

    public function test_find_filters_by_name(): void
    {
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $result = $this->action->find(1, 'John', 15);

        $this->assertCount(1, $result->items());
        $this->assertEquals('John Doe', $result->items()[0]->name);
    }

    public function test_find_filters_by_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);
        User::factory()->create(['email' => 'jane@example.com']);

        $result = $this->action->find(1, 'john@example.com', 15);

        $this->assertCount(1, $result->items());
        $this->assertEquals('john@example.com', $result->items()[0]->email);
    }

    public function test_find_returns_all_users_when_search_is_empty(): void
    {
        User::factory()->count(3)->create();

        $result = $this->action->find(1, '', 15);

        $this->assertEquals(3, $result->total());
    }

    public function test_find_one_returns_the_matching_user(): void
    {
        $user = User::factory()->create();

        $result = $this->action->findOne($user->id);

        $this->assertTrue($result->is($user));
    }

    public function test_find_one_throws_exception_when_user_does_not_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->action->findOne('999999');
    }

    public function test_find_one_does_not_return_soft_deleted_users(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->expectException(ModelNotFoundException::class);

        $this->action->findOne($user->id);
    }
}
