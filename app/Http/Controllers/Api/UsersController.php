<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\CreateUpdateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\FindUserAction;
use App\Actions\User\FormOptionsUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FindRequest;
use App\Http\Requests\Api\Users\CreateUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function __construct(
        private readonly FindUserAction $findUserAction,
        private readonly CreateUpdateUserAction $createUpdateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly FormOptionsUserAction $formOptionsAction,
    ) {}

    public function find(FindRequest $request): JsonResponse
    {
        $data = $request->validated();

        $items = $this->findUserAction->find(
            $data['page'] ?? 1,
            $data['query'] ?? '',
            $data['per_page'] ?? 15
        );

        $payload = $items->toArray();
        $payload['data'] = array_map(
            fn(User $user) => (new UserResource($user))->resolve($request),
            $items->getCollection()->all()
        );

        return response()->json($payload);
    }

    public function findOne(string $id): JsonResponse
    {
        $item = $this->findUserAction->findOne($id);

        return response()->json(new UserResource($item));
    }

    public function createUpdate(CreateUpdateUserRequest $request, ?string $id = null): JsonResponse
    {
        $data = $request->validated();

        $user_permissions = auth()->user()->getAllPermissions()->pluck('name');
        $item = $this->createUpdateUserAction->execute($data, $user_permissions, $id);

        return response()->json(new UserResource($item), $id === null ? 201 : 200);
    }

    public function toggleActive(string $id): JsonResponse
    {
        $user = $this->findUserAction->findOne($id);
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return response()->json(new UserResource($user));
    }

    public function delete(string $id): JsonResponse
    {
        $this->deleteUserAction->execute($id);

        return response()->json(['message' => 'Usuário removido com sucesso.']);
    }

    public function formOptions(): JsonResponse
    {
        $user_permissions = auth()->user()->getAllPermissions()->pluck('name');
        $payload = $this->formOptionsAction->execute($user_permissions->toArray());

        return response()->json($payload);
    }
}
