<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeTest extends TestCase
{

    protected bool $tenancy = true;

    public function test_authenticated_user_can_get_his_information(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson($this->baseUrl . '/api/auth/me')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            ]);
    }

    public function test_guest_cannot_access_me()
    {
        $this->getJson($this->baseUrl . '/api/auth/me')
            ->assertUnauthorized();
    }
}
