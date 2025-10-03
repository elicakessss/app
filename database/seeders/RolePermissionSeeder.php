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
            // Organization management  
            'manage organizations',
            'view organizations',
            'create organizations',
            'edit organizations',
            'delete organizations',
            
            // Student management
            'manage students',
            'view students',
            'create students',
            'edit students',
            'delete students',
            
            // Evaluation management
            'manage evaluations',
            'view evaluations',
            'create evaluations',
            'edit evaluations',
            'delete evaluations',
            
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
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin', 
            'guard_name' => 'web',
        ]);

        $adviserRole = Role::firstOrCreate([
            'name' => 'Adviser',
            'guard_name' => 'web',
        ]);

        // Assign permissions to roles
        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Adviser gets limited permissions for core functionality
        $adviserRole->syncPermissions([
            'manage organizations',
            'manage students', 
            'manage evaluations',
            'access admin panel',
            'view dashboard',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Admin and Adviser roles created');
    }
}
