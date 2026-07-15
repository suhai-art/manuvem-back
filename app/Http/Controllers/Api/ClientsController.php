<?php

namespace App\Http\Controllers\Api;

use App\Actions\Client\CreateUpdateClientAction;
use App\Actions\Client\DeleteClientAction;
use App\Actions\Client\FindClientAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FindRequest;
use App\Http\Requests\Api\Items\CreateUpdateItemRequest;
use Illuminate\Http\JsonResponse;

class ClientsController extends Controller
{
    public function __construct(
        private readonly FindClientAction $findClientAction,
        private readonly CreateUpdateClientAction $createUpdateClientAction,
        private readonly DeleteClientAction $deleteClientAction,
    ) {}

    public function find(FindRequest $request): JsonResponse
    {
        $data = $request->validated();

        $items = $this->findClientAction->find(
            $data['page'] ?? 1,
            $data['query'] ?? '',
            $data['per_page'] ?? 15
        );

        return response()->json($items);
    }

    public function findOne(string $id): JsonResponse
    {
        $item = $this->findClientAction->findOne($id);

        return response()->json($item);
    }

    public function createUpdate(CreateUpdateItemRequest $request, ?string $id = null): JsonResponse
    {
        $data = $request->validated();

        $item = $this->createUpdateClientAction->execute($data, $id);

        return response()->json($item, $id === null ? 201 : 200);
    }

    public function toggleActive(string $id): JsonResponse
    {
        return $this->delete($id);
    }

    public function delete(string $id): JsonResponse
    {
        $this->deleteClientAction->execute($id);

        return response()->json(['message' => 'Item removido com sucesso.']);
    }
}
