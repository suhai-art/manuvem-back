<?php

namespace App\Actions\User;

use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;

class FormOptionsUserAction
{
    public function execute(): array
    {
        $payload['roles'] = RoleResource::collection(Role::all());

        return $payload;
    }
}
