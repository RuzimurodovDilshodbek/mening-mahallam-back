<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $permissions = [
            'neighborhoods.create',
            'neighborhoods.edit',
            'neighborhoods.delete',
            'neighborhoods.view',
            'neighborhoods.publish',
            'neighborhoods.verify',
            'cameras.create',
            'cameras.edit',
            'cameras.delete',
            'pois.create',
            'pois.edit',
            'pois.delete',
            'users.view',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'neighborhoods.view',
            'neighborhoods.verify',
            'neighborhoods.publish',
            'users.view',
            'users.manage',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'neighborhoods.create',
            'neighborhoods.edit',
            'neighborhoods.delete',
            'neighborhoods.view',
            'cameras.create',
            'cameras.edit',
            'cameras.delete',
            'pois.create',
            'pois.edit',
            'pois.delete',
        ]);

        // Admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@mahalla.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@12345'),
            ]
        );
        $adminUser->assignRole('super-admin');

        // Test user
        $testUser = User::firstOrCreate(
            ['email' => 'user@mahalla.local'],
            [
                'name' => 'Test User',
                'password' => Hash::make('User@12345'),
            ]
        );
        $testUser->assignRole('user');
    }
}
