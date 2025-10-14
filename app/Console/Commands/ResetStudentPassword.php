<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetStudentPassword extends Command
{
    protected $signature = 'student:reset-password {email} {password}';
    protected $description = 'Reset a student password for testing';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $student = Student::where('email', $email)->first();
        
        if (!$student) {
            $this->error("Student with email {$email} not found!");
            return 1;
        }
        
        $student->update([
            'password' => Hash::make($password)
        ]);
        
        $this->info("Password updated for student: {$student->name} ({$email})");
        $this->info("New password: {$password}");
        
        return 0;
    }
}