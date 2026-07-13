<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
            'id' => 'test-tenant-' . uniqid(),
        ]);

        $domain = $this->tenant->id . '.test.local';

        $this->tenant->domains()->create([
            'domain' => $domain,
        ]);

        $this->baseUrl = 'http://' . $domain;

        tenancy()->initialize($this->tenant);

        $this->artisan('tenants:migrate', ['--tenants' => [$this->tenant->id]]);
    }
}
