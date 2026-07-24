<?php

namespace App\Actions\Role;

use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    public function execute(string $id): void
    {
        $role = Role::query()->findOrFail($id);

        $role->delete();
    }
}
