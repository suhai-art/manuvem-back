<?php

namespace App\Actions\Role;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateUpdateRoleAction
{
    /**
     * Create or update a role and sync its permissions.
     *
     * The `$data` array is expected to contain:
     *  - `name` (string, required)
     *  - `guard_name` (string, optional)
     *  - `permission` (array of permission ids, optional)
     *
     * Permissions are replaced wholesale (sync) with the provided ids, so the
     * caller always sends the full desired permission set for the role. When
     * `permission` is omitted the existing permissions are left untouched
     * (useful for renaming a role without resending all ids).
     *
     * @param  array{name: string, guard_name?: string, permission?: array<int>}  $data
     */
    public function execute(array $data, ?string $id = null): Role
    {
        $guard = $data['guard_name'] ?? config('auth.defaults.guard', 'web');

        if (! array_key_exists($guard, config('auth.guards', []))) {
            $guard = 'sanctum';
        }

        $permissionIds = $data['permission'] ?? null;
        unset($data['guard_name'], $data['permission']);

        $role = $id !== null
            ? Role::query()->findOrFail($id)
            : new Role;

        $role->fill($data);
        $role->guard_name = $guard;
        $role->save();

        if ($permissionIds !== null) {
            // Only keep permission ids that actually exist to avoid sync errors.
            $existingIds = Permission::query()
                ->whereIn('id', $permissionIds)
                ->pluck('id')
                ->all();

            $role->syncPermissions($existingIds);
        }

        return $role;
    }
}
