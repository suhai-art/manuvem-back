<?php

namespace App\Actions\Client;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DeleteClientAction
{
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            $client = Client::query()->findOrFail($id);
            $client->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
