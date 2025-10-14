<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class TestStudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'school_number' => '2024-001234',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}