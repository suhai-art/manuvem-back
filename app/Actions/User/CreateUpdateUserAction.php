<?php

namespace App\Actions\User;

use App\Models\User;

class CreateUpdateUserAction
{
    public function execute(array $data, ?string $id = null): User
    {
        $user = $id !== null
            ? User::query()->findOrFail($id)
            : new User();

        $user->fill($data);
        $user->save();

        return $user;
    }
}
