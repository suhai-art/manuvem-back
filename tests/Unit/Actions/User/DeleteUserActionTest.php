<?php

namespace Tests\Unit\Actions\User;

use App\Actions\User\DeleteUserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    protected bool $tenancy = true;

    private DeleteUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteUserAction();
    }

    public function test_execute_soft_deletes_the_user(): void
    {
        $user = User::factory()->create();

        $this->action->execute($user->id);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_execute_removes_user_from_default_query_results(): void
    {
        $user = User::factory()->create();

        $this->action->execute($user->id);

        $this->assertNull(User::query()->find($user->id));
    }

    public function test_execute_throws_exception_when_user_does_not_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->action->execute('999999');
    }

    public function test_execute_throws_exception_when_user_is_already_deleted(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->expectException(ModelNotFoundException::class);

        $this->action->execute($user->id);
    }
}
