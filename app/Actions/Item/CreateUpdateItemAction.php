<?php

namespace App\Actions\Item;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class CreateUpdateItemAction
{
    public function execute(array $data, ?string $id = null): Item
    {
        DB::beginTransaction();

        try {
            $item = $id !== null
                ? Item::query()->findOrFail($id)
                : new Item();

            $item->fill($data);
            $item->save();

            DB::commit();

            return $item;
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
