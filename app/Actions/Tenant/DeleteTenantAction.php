<?php

namespace App\Actions\Tenant;

use App\Models\Tenant;

class DeleteTenantAction
{
    public function execute(string $id): void
    {
        tenancy()->central(function () use ($id) {
            $item = Tenant::query()->findOrFail($id);
            $item->delete();
        });

        return;
    }
}
