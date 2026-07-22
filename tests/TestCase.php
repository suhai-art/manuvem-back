<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected bool $tenancy = false;

    protected ?Tenant $tenant = null;

    protected string $baseUrl = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        if ($this->tenancy) {
            $this->initializeTenancy();
            $this->seedPermissions();
        }
    }

    protected function tearDown(): void
    {
        if ($this->tenancy && $this->tenant) {
            tenancy()->end();
            $this->tenant->delete();
        }

        parent::tearDown();
    }

    protected function initializeTenancy(): void
    {
        $this->tenant = Tenant::create([
            'id' => 'test-tenant-'.uniqid(),
        ]);

        $domain = $this->tenant->id.'.test.local';

        $this->tenant->domains()->create([
            'domain' => $domain,
        ]);

        $this->baseUrl = 'http://'.$domain;

        tenancy()->initialize($this->tenant);

        $this->artisan('tenants:migrate', ['--tenants' => [$this->tenant->id]]);
    }

    /**
     * Create the spatie roles used across the suite inside the current
     * (tenant) database connection.
     */
    protected function seedRoles(array $roles = ['admin', 'user']): void
    {
        $guard = config('auth.defaults.guard', 'web');

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => $guard,
            ]);
        }
    }

    /**
     * Create a user and assign it a spatie role in a single call.
     */
    protected function createUserWithRole(string $role): User
    {
        $this->seedRoles([$role]);

        return User::factory()->create()->assignRole($role);
    }

    /**
     * Create a user WITHOUT any role (and therefore without permissions).
     * Useful to assert that protected routes are forbidden.
     */
    protected function createUserWithoutRole(): User
    {
        return User::factory()->create();
    }

    /**
     * Seed the spatie roles & permissions into the current (tenant) database
     * so that role-based permission checks (can:) work in feature tests.
     */
    protected function seedPermissions(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }
}
