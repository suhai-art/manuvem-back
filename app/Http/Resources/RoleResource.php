<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

class RoleResource extends JsonResource
{
    /**
     * Transform the role (with its permissions) into an array.
     *
     * Permissions are resolved through spatie/laravel-permission and returned
     * as a list of `{ id, name }` so the front-end can render and edit the
     * role's permission set directly.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        /** @var Role $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'permissions' => $this->permissions
                ->map(fn($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ])
                ->all(),
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
