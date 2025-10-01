<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create 20 students with fake data
        Student::factory(20)->create();
        
        // Create a few specific students for testing
        Student::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password'),
            'school_number' => 'STU001',
            'bio' => 'A dedicated student passionate about technology and learning.',
        ]);

        Student::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'password' => bcrypt('password'),
            'school_number' => 'STU002',
            'bio' => 'Creative and artistic student with a love for design and innovation.',
        ]);
    }
}
