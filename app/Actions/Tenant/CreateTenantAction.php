<?php

namespace App\Actions\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class CreateTenantAction
{
    public function execute(string $id, string $domain): Tenant
    {
        DB::beginTransaction();

        try {
            $tenant = Tenant::create([
                'id' => $id,
            ]);

            $tenant->domains()->create([
                'domain' => $domain,
            ]);

            DB::commit();

            return $tenant;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
