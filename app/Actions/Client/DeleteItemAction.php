<?php

namespace App\Actions\Client;

use App\Models\Client;

class DeleteClientAction
{
    public function execute(string $id): void
    {
        $item = Client::query()->findOrFail($id);

        $item->delete();
    }
}
