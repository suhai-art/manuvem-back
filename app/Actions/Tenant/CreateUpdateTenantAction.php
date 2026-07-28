<?php

namespace App\Actions\Tenant;

use App\Models\Tenant;

final class CreateUpdateTenantAction
{
    public function execute(array $data, ?string $id = null): Tenant
    {
        return tenancy()->central(function () use ($data, $id) {
            $tenant = $id
                ? Tenant::query()->findOrNew($id)
                : new Tenant();

            $tenant->id = $data['id'];
            $tenant->name = $data['name'];
            $tenant->save();

            if (isset($data['domains'])) {
                $this->syncDomains($tenant, $data['domains']);
            }

            return $tenant->fresh('domains');
        });
    }

    private function syncDomains(Tenant $tenant, array|string $domains): void
    {
        $domains = is_array($domains) ? $domains : [$domains];

        $tenant->domains()->delete();

        foreach ($domains as $domain) {
            $tenant->domains()->create(['domain' => $domain]);
        }
    }
}
