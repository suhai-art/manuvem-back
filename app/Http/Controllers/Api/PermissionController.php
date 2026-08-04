<?php

namespace App\Http\Controllers\Api;

use App\Actions\Role\CreateUpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Permission\PermissionResource;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function __construct(
        private readonly CreateUpdateRoleAction $createUpdateRoleAction,
    ) {}

    /**
     * List all available permissions (read-only).
     */
    public function index(Request $request): JsonResponse
    {
        $permissions = Permission::query()->orderBy('name')->get();

        return response()->json(PermissionResource::collection($permissions)->resolve($request));
    }

    /**
     * Listing a single permission is not exposed as a separate endpoint;
     * this stub keeps the resource route symmetry but returns 405 to match
     * the "creation/standalone endpoints removed" contract.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Criação de permissões não é permitida.'], 405);
    }

    /**
     * Create a new role (requires permissions.manage).
     */
    public function createRole(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'guard_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $data = $request->only(['name', 'guard_name']);
        $data['permission'] = [];

        $role = $this->createUpdateRoleAction->execute($data);

        return response()->json(new RoleResource($role), 201);
    }

    /**
     * Attach a single existing permission (by name) to a role.
     * Rejects permission names that do not already exist (no auto-create).
     */
    public function attachPermission(Role $role, Request $request): JsonResponse
    {
        $request->validate([
            'permission' => ['required', 'string'],
        ]);

        $permission = Permission::query()
            ->where('name', $request->input('permission'))
            ->first();

        if ($permission === null) {
            return response()->json([
                'message' => 'Permissão inexistente.',
            ], 422);
        }

        $role->givePermissionTo($permission);

        return response()->json(new RoleResource($role->load('permissions')));
    }

    /**
     * Sync a role's permissions using a list of permission names.
     * Permissions that do not exist are reported in `missing_permissions`
     * and are NOT created.
     */
    public function syncPermissions(Role $role, Request $request): JsonResponse
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string'],
        ]);

        $names = $request->input('permissions', []);

        $existing = Permission::query()
            ->whereIn('name', $names)
            ->get();

        $existingNames = $existing->pluck('name')->all();
        $missing = array_values(array_diff($names, $existingNames));

        $role->syncPermissions($existing);

        return response()->json(
            (new RoleResource($role->load('permissions')))->resolve($request)
            + ['missing_permissions' => $missing]
        );
    }
}
