<?php

namespace App\Actions\Role;

use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Permission;

class FormOptionsRolesAction
{
    public function execute(): array
    {
        $payload['permissions'] = PermissionResource::collection(Permission::query()->get());

        return $payload;
    }
}
