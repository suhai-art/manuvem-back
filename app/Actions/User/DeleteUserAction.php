<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserAction
{
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            $user = User::query()->findOrFail($id);
            $user->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
