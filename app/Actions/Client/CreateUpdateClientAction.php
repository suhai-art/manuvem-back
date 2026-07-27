<?php

namespace App\Actions\Client;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class CreateUpdateClientAction
{
    public function execute(array $data, ?string $id = null): Client
    {
        DB::beginTransaction();

        try {
            $client = $id !== null
                ? Client::query()->findOrFail($id)
                : new Client();

            $client->fill($data);
            $client->save();

            DB::commit();

            return $client;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
