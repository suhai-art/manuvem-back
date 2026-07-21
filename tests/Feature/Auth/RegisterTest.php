<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_register_endpoint_is_removed(): void
    {
        $response = $this->postJson($this->baseUrl . '/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertNotFound();
    }
}
