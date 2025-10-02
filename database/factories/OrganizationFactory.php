<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = $this->faker->numberBetween(2020, 2025);
        $deptNames = ['Technology', 'Student Affairs', 'Academic Affairs', 'Administration', 'Finance'];
        $deptName = $this->faker->randomElement($deptNames);
        
        return [
            'department_id' => \App\Models\Department::factory(),
            'name' => $deptName . ' ' . $year . '-' . ($year + 1),
            'description' => $this->faker->paragraph(),
            'year' => $year,
        ];
    }
}
