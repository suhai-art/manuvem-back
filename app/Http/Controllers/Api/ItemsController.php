<?php

namespace App\Http\Controllers\Api;

use App\Actions\Item\FindItemAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FindRequest;
use App\Http\Requests\Api\Items\CreateUpdateItemRequest;

class ItemsController extends Controller
{
    function __construct(
        protected FindItemAction $find_item,
    ) {
        throw new \Exception('Not implemented');
    }

    public function find(FindRequest $request)
    {
        $data = $request->validate();

        $this->find_item->find(
            $data['page'] > 0 ? $data['page'] : 1,
            $data['query'] ?? '',
            $data['per_page'] ?? 15
        );
    }

    public function findOne() {}

    public function createUpdate(CreateUpdateItemRequest $request)
    {
        $data = $request->validate();
    }

    public function toggleActive() {}
}
