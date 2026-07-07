<?php

namespace Database\Seeders;

use App\Actions\Tenant\CreateTenantAction;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function __construct(
        private readonly CreateTenantAction $createTenantAction,
    ) {}

    public function run(): void
    {
        $this->createTenantAction->execute(
            id: 'tenant1',
            domain: 'tenant1.localhost',
        );
    }
}
