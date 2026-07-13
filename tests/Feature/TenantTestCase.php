<?php

namespace Tests\Feature;

use App\Actions\Tenant\CreateTenantAction;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

abstract class TenantTestCase extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = app(CreateTenantAction::class)->execute(
            'tenant-test',
            'tenant.test'
        );

        tenancy()->initialize($this->tenant);
        Artisan::call('tenants:migrate', [
            '--tenants' => [$this->tenant->id],
        ]);
    }

    protected function tearDown(): void
    {
        tenancy()->end();

        parent::tearDown();
    }
}
