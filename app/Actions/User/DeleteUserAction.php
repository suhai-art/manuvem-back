<?php

namespace App\Actions\User;

use App\Models\User;

class DeleteUserAction
{
    public function execute(string $id): void
    {
        $user = User::query()->findOrFail($id);
        $user->delete();
    }
}
