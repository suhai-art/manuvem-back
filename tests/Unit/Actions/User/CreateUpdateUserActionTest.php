<?php

namespace Tests\Unit\Actions\User;

use App\Actions\User\CreateUpdateUserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class CreateUpdateUserActionTest extends TestCase
{
    protected bool $tenancy = true;

    private CreateUpdateUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateUpdateUserAction();
    }

    public function test_execute_creates_a_new_user_when_no_id_is_given(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ];

        $user = $this->action->execute($data);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_execute_returns_a_user_instance_with_the_given_data(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'inactive',
        ];

        $user = $this->action->execute($data);

        $this->assertEquals('Jane Doe', $user->name);
        $this->assertEquals('jane@example.com', $user->email);
        $this->assertEquals('user', $user->role);
        $this->assertEquals('inactive', $user->status);
    }

    public function test_execute_updates_an_existing_user_when_id_is_given(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $updated = $this->action->execute([
            'name' => 'New Name',
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
            'status' => $user->status,
        ], $user->id);

        $this->assertTrue($updated->is($user));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_execute_does_not_create_a_new_row_when_updating(): void
    {
        $user = User::factory()->create();

        $this->action->execute([
            'name' => 'Updated Name',
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
            'status' => $user->status,
        ], $user->id);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_execute_throws_exception_when_updating_a_nonexistent_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->action->execute([
            'name' => 'Nonexistent',
            'email' => 'nonexistent@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'status' => 'active',
        ], '999999');
    }
}
