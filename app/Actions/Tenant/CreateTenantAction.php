<?php

namespace App\Actions\Tenant;

use App\Models\Tenant;

class CreateTenantAction
{
    public function execute(string $id, string $domain): Tenant
    {
        $tenant = Tenant::create([
            'id' => $id,
        ]);

        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        return $tenant;
    }
}
