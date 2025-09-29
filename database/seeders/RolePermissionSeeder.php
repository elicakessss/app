<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // General admin permissions
            'access admin panel',
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin', 
            'guard_name' => 'web',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        // Assign permissions to roles
        // Super admin gets all permissions
        $superAdminRole->syncPermissions(Permission::all());

        // Admin gets most permissions except deleting other admins
        $adminRole->syncPermissions([
            'view users',
            'create users', 
            'edit users',
            'view roles',
            'view permissions',
            'access admin panel',
            'view dashboard',
        ]);

        // Regular user gets basic permissions
        $userRole->syncPermissions([
            'view dashboard',
        ]);

        // Create default admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Super Administrator',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign super-admin role to admin user
        $adminUser->assignRole($superAdminRole);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Admin user created: admin@admin.com / password');
    }
}
