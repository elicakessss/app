<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            ['name' => 'School of Information Technology and Engineering', 'abbreviation' => 'SITE'],
            ['name' => 'Student Council', 'abbreviation' => 'SC'],
            ['name' => 'Human Resources Department', 'abbreviation' => 'HRD'],
            ['name' => 'Finance and Budget Office', 'abbreviation' => 'FBO'],
            ['name' => 'School of Business and Management', 'abbreviation' => 'SBM'],
            ['name' => 'Research and Development Division', 'abbreviation' => 'RDD'],
            ['name' => 'Marketing and Communications', 'abbreviation' => 'MAC'],
            ['name' => 'Academic Affairs Office', 'abbreviation' => 'AAO'],
        ];
        
        $dept = $this->faker->unique()->randomElement($departments);
        
        return [
            'name' => $dept['name'],
            'abbreviation' => $dept['abbreviation'],
            'description' => $this->faker->paragraph(),
        ];
    }
}
