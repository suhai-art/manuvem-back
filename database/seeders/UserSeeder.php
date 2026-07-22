<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        Role::firstOrCreate(['name' => 'root', 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);

        $root = User::firstOrCreate(
            ['email' => 'root@example.com'],
            [
                'name' => 'Root',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        $root->assignRole('root');

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]
        );
        $admin->assignRole('admin');
    }
}
