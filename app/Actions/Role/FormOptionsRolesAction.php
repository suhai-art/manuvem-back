<?php

namespace App\Actions\Role;

use App\Http\Resources\Permission\PermissionResource;
use App\Support\Permissions;
use Spatie\Permission\Models\Permission;

class FormOptionsRolesAction
{
    public function execute(): array
    {
        $permissions = Permission::query()
            ->get()
            ->filter(function (Permission $permission) {
                $module = explode('.', $permission->name)[0];

                return !in_array($module, Permissions::ROOT_MODULES);
            });

        return [
            'permissions' => PermissionResource::collection($permissions),
        ];
    }
}
