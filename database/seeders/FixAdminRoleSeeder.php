<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FixAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Find the admin user
        $adminUser = User::where('email', 'admin@admin.com')->first();

        if ($adminUser) {
            // Remove all roles and assign only admin role
            $adminUser->syncRoles(['admin']);
            $this->command->info('Admin user role updated to: admin');
        } else {
            $this->command->info('Admin user not found!');
        }
    }
}