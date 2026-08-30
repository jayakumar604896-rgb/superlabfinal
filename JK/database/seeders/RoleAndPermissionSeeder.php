<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            'view dashboard',
            'manage users',
            'manage roles',
            'manage customers',
            'manage categories',
            'manage services',
            'manage tests',
            'manage test categories',
            'manage pages',
            'manage blogs',
            'manage gallery',
            'manage enquiries',
            'manage testimonials',
            'manage settings',
            'manage locations',
            'manage packages',
            'manage payment types',
            'manage bookings',
            'manage payments',
            'manage payment gateways',
            'manage coupons',
            'manage service reviews',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        // Super Admin gets all permissions (Spatie supports returning true via Gate::before, but we'll assign them directly too)
        $superAdminRole->syncPermissions($permissions);

        // Admin gets most permissions
        $adminRole->syncPermissions([
            'view dashboard',
            'manage customers',
            'manage categories',
            'manage services',
            'manage pages',
            'manage blogs',
            'manage gallery',
            'manage enquiries',
            'manage testimonials',
            'manage settings',
            'manage locations',
            'manage packages',
            'manage payment types',
            'manage bookings',
            'manage payments',
            'manage payment gateways',
            'manage coupons',
            'manage service reviews',
        ]);

        // Editor gets content creation permissions
        $editorRole->syncPermissions([
            'view dashboard',
            'manage customers',
            'manage categories',
            'manage services',
            'manage pages',
            'manage blogs',
            'manage gallery',
            'manage testimonials',
            'manage service reviews',
            'manage locations',
            'manage packages',
            'manage payment types',
            'manage bookings',
            'manage payments',
            'manage payment gateways',
            'manage coupons',
        ]);

        // Staff gets minimal dashboard view and enquiries
        $staffRole->syncPermissions([
            'view dashboard',
            'manage enquiries'
        ]);

        // Create Super Admin User
        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@superlab.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $superAdminUser->assignRole($superAdminRole);

        // Create a test Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@superlab.com'],
            [
                'name' => 'General Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create a test Editor User
        $editorUser = User::updateOrCreate(
            ['email' => 'editor@superlab.com'],
            [
                'name' => 'Content Editor',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $editorUser->assignRole($editorRole);

        // Create a test Staff User
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@superlab.com'],
            [
                'name' => 'Support Staff',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $staffUser->assignRole($staffRole);
    }
}
