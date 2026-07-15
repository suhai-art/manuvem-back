<?php

namespace App\Actions\Client;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class FindClientAction
{
    public function findOne(string $id): Client
    {
        return Client::query()->findOrFail($id);
    }

    public function find(int $page, string $search, int $perPage): LengthAwarePaginator
    {
        $query = Client::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%");
            });
        }

        return $query->paginate(
            perPage: $perPage,
            page: $page
        );
    }
}
