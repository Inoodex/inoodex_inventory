<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Comprehensive application permissions list
        $permissions = [
            'Administration',
            'Settings',
            'Category Management',
            'Product Management',
            'Customer Management',
            'Vendor Management',
            'Purchase Management',
            'Inventory Management',
            'Warranty Management',
            'Service Management',
            'Sales Management',
            'Accounts Management',
            'Expense Management',
            'Payment Management',
            'Project Management',
            'Client Management',
            'Cost Management',
            'Company Management',
            'Report Management',
            'Employee Management',
            'Booking',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 3. Define Roles
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $employeeRole = Role::firstOrCreate([
            'name' => 'Employee',
            'guard_name' => 'web',
        ]);

        // 4. Assign ALL permissions to Super Admin
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // 5. Assign operational permissions to Admin
        $adminPermissions = Permission::whereNotIn('name', [
            'Administration',
            'Settings',
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // 6. Assign Super Admin role to default admin user(s)
        $defaultUsers = User::whereIn('email', ['hello@inoodex.com', 'admin@example.com'])->get();
        foreach ($defaultUsers as $user) {
            if (!$user->hasRole('Super Admin')) {
                $user->assignRole($superAdminRole);
            }
        }

        // Fallback: Ensure the first user in database has Super Admin role
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('Super Admin')) {
            $firstUser->assignRole($superAdminRole);
        }

        // 7. Clear cache again after seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
