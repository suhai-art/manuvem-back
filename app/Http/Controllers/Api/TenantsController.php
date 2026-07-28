<?php

namespace App\Http\Controllers\Api;

use App\Actions\Tenant\CreateUpdateTenantAction;
use App\Actions\Tenant\DeleteTenantAction;
use App\Actions\Tenant\FindTenantAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FindRequest;
use App\Http\Requests\Api\Tenants\CreateUpdateTenantRequest;
use App\Http\Resources\ItemResource;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class TenantsController extends Controller
{
    public function __construct(
        private readonly FindTenantAction $findTenantAction,
        private readonly CreateUpdateTenantAction $createUpdateTenantAction,
        private readonly DeleteTenantAction $deleteTenantAction,
    ) {}

    public function find(FindRequest $request): JsonResponse
    {
        $data = $request->validated();

        $tenants = $this->findTenantAction->find(
            $data['page'] ?? 1,
            $data['query'] ?? '',
            $data['per_page'] ?? 15
        );

        $payload = $tenants->toArray();
        $payload['data'] = array_map(
            fn(Tenant $item) => (new TenantResource($item))->resolve($request),
            $tenants->getCollection()->all()
        );

        return response()->json($payload);
    }

    public function findOne(string $id): JsonResponse
    {
        $tenant = $this->findTenantAction->findOne($id);

        return response()->json(new ItemResource($tenant));
    }

    public function createUpdate(CreateUpdateTenantRequest $request, ?string $id = null): JsonResponse
    {
        $data = $request->validated();

        $tenant = $this->createUpdateTenantAction->execute($data, $id);

        return response()->json(new TenantResource($tenant), $id === null ? 201 : 200);
    }

    public function toggleActive(string $id): JsonResponse
    {
        return $this->delete($id);
    }

    public function delete(string $id): JsonResponse
    {
        $this->deleteTenantAction->execute($id);

        return response()->json(['message' => 'Tenant removido com sucesso.']);
    }
}
