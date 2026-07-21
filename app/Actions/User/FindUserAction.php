<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class FindUserAction
{
    public function findOne(string $id): User
    {
        return User::query()->findOrFail($id);
    }

    public function find(int $page, string $search, int $perPage): LengthAwarePaginator
    {
        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate(
            perPage: $perPage,
            page: $page
        );
    }
}
