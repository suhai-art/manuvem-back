<?php

namespace App\Actions\Role;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateUpdateRoleAction
{
    public function execute(array $data, ?string $id = null): Role
    {
        DB::beginTransaction();

        try {
            $guard = $data['guard_name'] ?? config('auth.defaults.guard', 'sanctum');

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
                $existingIds = Permission::query()
                    ->whereIn('id', $permissionIds)
                    ->pluck('id')
                    ->all();

                $role->syncPermissions($existingIds);
            }

            DB::commit();

            return $role;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
