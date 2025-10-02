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
        // First create some departments
        $departments = [
            [
                'name' => 'School of Information Technology and Engineering',
                'abbreviation' => 'SITE',
                'description' => ' ',
            ],
            [
                'name' => 'School of Art Sciences and Teacher Education',
                'abbreviation' => 'SASTE',
                'description' => ' ',
            ],
            [
                'name' => 'School of Business and Hospitality Management',
                'abbreviation' => 'SBAHM',
                'description' => ' ',
            ],
            [
                'name' => 'School of Nursing and Allied Health Sciences',
                'abbreviation' => 'SNAHS',
                'description' => ' ',
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

    }
}
