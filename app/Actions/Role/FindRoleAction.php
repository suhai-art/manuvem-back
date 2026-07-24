<?php

namespace App\Actions\Role;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class FindRoleAction
{
    public function findOne(string $id): Role
    {
        return Role::query()->with('permissions')->findOrFail($id);
    }

    public function find(int $page, string $search, int $perPage): LengthAwarePaginator
    {
        $query = Role::query()->with('permissions');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%");
            });
        }

        return $query->paginate(
            perPage: $perPage,
            page: $page
        );
    }
}
