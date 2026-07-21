<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->postJson($this->baseUrl . '/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertJson([
            'success' => true
        ]);
        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['token'],
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson($this->baseUrl . '/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong_pass',
        ]);

        $response->assertUnauthorized();
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson($this->baseUrl . '/api/auth/login', []);

        $response->assertStatus(422);
    }
}
