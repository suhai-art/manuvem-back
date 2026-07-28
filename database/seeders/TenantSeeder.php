<?php

namespace Database\Seeders;

use App\Actions\Tenant\CreateUpdateTenantAction;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function __construct(
        private readonly CreateUpdateTenantAction $createTenantAction,
    ) {}

    public function run(): void
    {
        $this->createTenantAction->execute(
            [
                'id' => 'example',
                'name' => 'Exemplo Tenant',
                'domains' => ['example.localhost']
            ],
            'example'
        );
    }
}
