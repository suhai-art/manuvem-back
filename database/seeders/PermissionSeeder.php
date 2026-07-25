<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'sanctum');

        $modules = [
            'client' => ['view', 'create', 'update', 'delete'],
            'item' => ['view', 'create', 'update', 'delete'],
            'user' => ['view', 'create', 'update', 'delete'],
            'tenant' => ['view', 'create', 'update', 'delete'],
            'role' => ['view', 'create', 'update', 'delete'],
            'permissions' => ['view', 'manage'],
        ];

        $root_modules = [
            'permissions',
            'tenant'
        ];

        $permissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permission = Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => $guard,
                ]);

                if (!in_array($modules, $root_modules)) {
                    $permissions[] = $permission;
                }
            }
        }

        $root = Role::firstOrCreate([
            'name' => 'root',
            'guard_name' => $guard,
        ]);

        $rootPermission = Permission::firstOrCreate([
            'name' => '*',
            'guard_name' => $guard,
        ]);

        $root->syncPermissions([$rootPermission]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => $guard,
        ]);

        $admin->syncPermissions($permissions);
    }
}
