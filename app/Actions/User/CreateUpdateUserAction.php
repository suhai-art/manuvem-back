<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

use Spatie\Permission\Models\Role;

class CreateUpdateUserAction
{
    public function execute(array $data, array $user_permissions, ?string $id = null): User
    {
        DB::beginTransaction();
        try {
            $roleId = $data['role'] ?? null;
            unset($data['role']);

            $user = $id !== null
                ? User::findOrFail($id)
                : new User();

            $user->fill($data);
            $user->save();

            if ($roleId) {
                $role = Role::findOrFail($roleId);
                if (! in_array("role.assign.{$role->name}", $user_permissions)) {
                    abort(403, 'Você não possui permissão para atribuir esta role.');
                }

                $user->syncRoles($role);
            }

            DB::commit();

            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
