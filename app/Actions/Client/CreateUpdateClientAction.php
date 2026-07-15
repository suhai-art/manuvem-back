<?php

namespace App\Actions\Item;

use App\Models\Item;

class CreateUpdateItemAction
{
    public function execute(array $data, ?string $id = null): Item
    {
        $item = $id !== null
            ? Item::query()->findOrFail($id)
            : new Item();

        $item->fill($data);
        $item->save();

        return $item;
    }
}
