<?php

namespace App\Actions\User;

use App\Http\Resources\Role\RoleResource;
use Spatie\Permission\Models\Role;

class FormOptionsUserAction
{
    public function execute(): array
    {
        $roles = Role::query()
            ->get();
        $payload['roles'] = RoleResource::collection($roles);

        return $payload;
    }
}
