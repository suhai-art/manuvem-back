<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the user (with roles & permissions) into an array.
     *
     * Roles and permissions are resolved through spatie/laravel-permission.
     * `permissions` aggregates direct permissions + those inherited from roles,
     * so the front-end can gate UI without an extra request.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'status'        => $this->status,
            'roles'         => $this->getRoleNames()->toArray(),
            'permissions'   => $this->getAllPermissions()->pluck('name')->toArray(),
        ];
    }
}
