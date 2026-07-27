<?php

namespace App\Actions\User;

use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;

class FormOptionsUserAction
{
    public function execute(array $user_permissions): array
    {
        $roles = Role::query()
            ->whereHas('permissions', function ($query) use ($user_permissions) {
                $query->whereIn('name', $user_permissions);
            })
            ->get();
        $payload['roles'] = RoleResource::collection($roles);

        return $payload;
    }
}
