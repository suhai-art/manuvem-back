<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\CreateUpdateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\FindUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FindRequest;
use App\Http\Requests\Api\Users\CreateUpdateUserRequest;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    public function __construct(
        private readonly FindUserAction $findUserAction,
        private readonly CreateUpdateUserAction $createUpdateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
    ) {}

    public function find(FindRequest $request): JsonResponse
    {
        $data = $request->validated();

        $items = $this->findUserAction->find(
            $data['page'] ?? 1,
            $data['query'] ?? '',
            $data['per_page'] ?? 15
        );

        return response()->json($items);
    }

    public function findOne(string $id): JsonResponse
    {
        $item = $this->findUserAction->findOne($id);

        return response()->json($item);
    }

    public function createUpdate(CreateUpdateUserRequest $request, ?string $id = null): JsonResponse
    {
        $data = $request->validated();

        $item = $this->createUpdateUserAction->execute($data, $id);

        return response()->json($item, $id === null ? 201 : 200);
    }

    public function toggleActive(string $id): JsonResponse
    {
        $user = $this->findUserAction->findOne($id);
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return response()->json($user);
    }

    public function delete(string $id): JsonResponse
    {
        $this->deleteUserAction->execute($id);

        return response()->json(['message' => 'Usuário removido com sucesso.']);
    }
}
