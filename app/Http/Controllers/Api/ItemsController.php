<?php

namespace App\Http\Controllers\Api;

use App\Actions\Item\CreateUpdateItemAction;
use App\Actions\Item\DeleteItemAction;
use App\Actions\Item\FindItemAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FindRequest;
use App\Http\Requests\Api\Items\CreateUpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;

class ItemsController extends Controller
{
    public function __construct(
        private readonly FindItemAction $findItemAction,
        private readonly CreateUpdateItemAction $createUpdateItemAction,
        private readonly DeleteItemAction $deleteItemAction,
    ) {}

    public function find(FindRequest $request): JsonResponse
    {
        $data = $request->validated();

        $items = $this->findItemAction->find(
            $data['page'] ?? 1,
            $data['query'] ?? '',
            $data['per_page'] ?? 15
        );

        $payload = $items->toArray();
        $payload['data'] = array_map(
            fn (Item $item) => (new ItemResource($item))->resolve($request),
            $items->getCollection()->all()
        );

        return response()->json($payload);
    }

    public function findOne(string $id): JsonResponse
    {
        $item = $this->findItemAction->findOne($id);

        return response()->json(new ItemResource($item));
    }

    public function createUpdate(CreateUpdateItemRequest $request, ?string $id = null): JsonResponse
    {
        $data = $request->validated();

        $item = $this->createUpdateItemAction->execute($data, $id);

        return response()->json(new ItemResource($item), $id === null ? 201 : 200);
    }

    public function toggleActive(string $id): JsonResponse
    {
        return $this->delete($id);
    }

    public function delete(string $id): JsonResponse
    {
        $this->deleteItemAction->execute($id);

        return response()->json(['message' => 'Item removido com sucesso.']);
    }
}
