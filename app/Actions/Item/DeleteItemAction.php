<?php

namespace App\Actions\Item;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class DeleteItemAction
{
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            $item = Item::query()->findOrFail($id);
            $item->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
