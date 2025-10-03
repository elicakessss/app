<?php

namespace Database\Seeders;

use App\Models\Department;
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
        // First create the departments
        $departments = [
            [
                'name' => 'School of Information Technology and Engineering',
                'abbreviation' => 'SITE',
                'description' => 'Information Technology and Engineering Department',
            ],
            [
                'name' => 'School of Arts Sciences and Teacher Education',
                'abbreviation' => 'SASTE',
                'description' => 'Arts Sciences and Teacher Education Department',
            ],
            [
                'name' => 'School of Business, Accountancy, and Hospitality Management',
                'abbreviation' => 'SBAHM',
                'description' => 'Business, Accountancy, and Hospitality Management Department',
            ],
            [
                'name' => 'School of Nursing and Allied Health Sciences',
                'abbreviation' => 'SNAHS',
                'description' => 'Nursing and Allied Health Sciences Department',
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

    }
}
