<?php

namespace Tests\Feature;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    protected bool $tenancy = true;

    public function test_admin_user_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson($this->baseUrl . '/api/users');

        $response->assertOk();
    }

    public function test_non_admin_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $response = $this->getJson($this->baseUrl . '/api/users');

        $response->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'Acesso negado. Permissão insuficiente.',
            ]);
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->getJson($this->baseUrl . '/api/users');

        $response->assertUnauthorized();
    }

    public function test_middleware_allows_correct_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $request = Request::create('/api/users', 'GET');
        $request->setUserResolver(fn () => $admin);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn () => response()->json(['success' => true]), 'admin');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_middleware_denies_wrong_role(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $request = Request::create('/api/users', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn () => response()->json(['success' => true]), 'admin');

        $this->assertEquals(403, $response->getStatusCode());
    }
}
