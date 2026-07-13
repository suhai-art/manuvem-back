<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson($this->baseUrl . '/api/auth/logout');

        $response->assertOk();
    }

    public function test_guest_cannot_logout(): void
    {
        $this->postJson($this->baseUrl . '/api/auth/logout')
            ->assertUnauthorized();
    }
}
