<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

use Spatie\Permission\Models\Role;

class CreateUpdateUserAction
{
    public function execute(array $data, ?string $id = null): User
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
