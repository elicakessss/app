<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestStudentAuth extends Command
{
    protected $signature = 'student:test-auth {email} {password}';
    protected $description = 'Test student authentication';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $this->info("Testing student authentication...");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        
        if (Auth::guard('student')->attempt(['email' => $email, 'password' => $password])) {
            $student = Auth::guard('student')->user();
            $this->info("✅ Authentication SUCCESSFUL!");
            $this->info("Student: {$student->name} (ID: {$student->id})");
            $this->info("School Number: {$student->school_number}");
        } else {
            $this->error("❌ Authentication FAILED!");
            
            $student = Student::where('email', $email)->first();
            if ($student) {
                $this->info("Student exists: {$student->name}");
                $this->error("Password is incorrect or student guard is not working properly.");
            } else {
                $this->error("Student with email {$email} does not exist!");
            }
        }
        
        Auth::guard('student')->logout();
        
        return 0;
    }
}