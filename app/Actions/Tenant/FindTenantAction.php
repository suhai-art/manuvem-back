<?php

namespace App\Actions\Tenant;

use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

class FindTenantAction
{
    public function findOne(string $id): Tenant
    {
        return tenancy()->central(function () use ($id) {
            return Tenant::query()->findOrFail($id);
        });
    }

    public function find(int $page, string $search, int $perPage): LengthAwarePaginator
    {
        return tenancy()->central(function () use ($page, $search, $perPage) {

            $query = Tenant::query();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            return $query->paginate(
                perPage: $perPage,
                page: $page
            );
        });
    }
}
