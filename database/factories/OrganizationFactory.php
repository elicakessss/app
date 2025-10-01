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
        $orgTypes = ['Department', 'Division', 'Office', 'Bureau', 'Unit'];
        $orgType = $this->faker->randomElement($orgTypes);
        $name = $orgType . ' of ' . $this->faker->words(2, true);
        
        return [
            'name' => $name,
            'code' => strtoupper($this->faker->lexify('???')) . $year,
            'description' => $this->faker->paragraph(),
            'year' => $year,
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
        ];
    }
}
