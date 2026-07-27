<?php

namespace App\Http\Controllers\Api;

use App\Actions\Role\CreateUpdateRoleAction;
use App\Actions\Role\DeleteRoleAction;
use App\Actions\Role\FindRoleAction;
use App\Actions\Role\FormOptionsRolesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FindRequest;
use App\Http\Requests\Api\Roles\CreateUpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct(
        private readonly FindRoleAction $findRoleAction,
        private readonly CreateUpdateRoleAction $createUpdateRoleAction,
        private readonly DeleteRoleAction $deleteRoleAction,
        private readonly FormOptionsRolesAction $formOptionsActions,
    ) {}

    public function find(FindRequest $request): JsonResponse
    {
        $data = $request->validated();

        $items = $this->findRoleAction->find(
            $data['page'] ?? 1,
            $data['query'] ?? '',
            $data['per_page'] ?? 15
        );

        $payload = $items->toArray();
        $payload['data'] = array_map(
            fn(Role $role) => (new RoleResource($role))->resolve($request),
            $items->getCollection()->all()
        );

        return response()->json($payload);
    }

    public function findOne(string $id): JsonResponse
    {
        $item = $this->findRoleAction->findOne($id);

        return response()->json(new RoleResource($item));
    }

    public function createUpdate(CreateUpdateRoleRequest $request, ?string $id = null): JsonResponse
    {
        $data = $request->validated();

        $item = $this->createUpdateRoleAction->execute($data, $id);

        return response()->json(new RoleResource($item), $id === null ? 201 : 200);
    }

    public function delete(string $id): JsonResponse
    {
        $role = Role::query()->findOrFail($id);

        $assigned = User::query()
            ->whereHas('roles', fn($q) => $q->where('role_id', $role->id))
            ->exists();

        if ($assigned) {
            return response()->json(
                ['message' => 'Não é possível remover o papel: ele ainda está atribuído a usuários.'],
                409
            );
        }

        $this->deleteRoleAction->execute($id);

        return response()->json(['message' => 'Papel removido com sucesso.']);
    }

    public function formOptions(): JsonResponse
    {
        $payload = $this->formOptionsActions->execute();
        return response()->json(
            $payload
        );
    }
}
