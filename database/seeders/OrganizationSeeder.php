<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample organizations
        $organizations = [
            [
                'name' => 'Department of Information Technology',
                'code' => 'DIT2024',
                'description' => 'Responsible for IT infrastructure and digital services',
                'year' => 2024,
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources Division',
                'code' => 'HRD2024',
                'description' => 'Manages human resources and employee development',
                'year' => 2024,
                'is_active' => true,
            ],
            [
                'name' => 'Finance and Budget Office',
                'code' => 'FBO2024',
                'description' => 'Handles financial planning and budget management',
                'year' => 2024,
                'is_active' => true,
            ],
            [
                'name' => 'Legal Affairs Unit',
                'code' => 'LAU2024',
                'description' => 'Provides legal counsel and compliance oversight',
                'year' => 2024,
                'is_active' => true,
            ],
            [
                'name' => 'Public Relations Bureau',
                'code' => 'PRB2024',
                'description' => 'Manages public communications and media relations',
                'year' => 2024,
                'is_active' => false, // Inactive for testing
            ],
            [
                'name' => 'Administrative Services',
                'code' => 'ADM2023',
                'description' => 'General administrative support and services',
                'year' => 2023,
                'is_active' => true,
            ],
        ];

        foreach ($organizations as $org) {
            Organization::create($org);
        }

        // Create some additional organizations using factory
        Organization::factory(5)->create();
    }
}
