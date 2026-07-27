<?php

namespace App\Actions\Role;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            $role = Role::query()->findOrFail($id);

            $role->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
