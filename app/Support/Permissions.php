<?php

namespace App\Support;

class Permissions
{

    public const MODULES = [
        'client' => ['view', 'create', 'update', 'delete'],
        'item' => ['view', 'create', 'update', 'delete'],
        'user' => ['view', 'create', 'update', 'delete'],
        'tenant' => ['view', 'create', 'update', 'delete'],
        'role' => ['view', 'create', 'update', 'delete'],
        'permissions' => ['view', 'manage'],
    ];

    public const ROOT_MODULES = [
        'permissions',
        'tenant',
        '*'
    ];
}
