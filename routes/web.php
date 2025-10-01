<?php

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\StudentRegistrationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route - redirects based on guard
Route::get('/dashboard', function () {
    // If student is logged in, redirect to student dashboard
    if (Auth::guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }
    
    // If admin is logged in, redirect to Filament admin panel
    if (Auth::guard('web')->check()) {
        return redirect('/admin');
    }
    
    // If no one is logged in, redirect to welcome
    return redirect('/');
})->middleware(['auth:web,student'])->name('dashboard');

// Student Authentication Routes
Route::prefix('student')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.post');
    Route::get('/register', [StudentRegistrationController::class, 'create'])->name('student.register');
    Route::post('/register', [StudentRegistrationController::class, 'store'])->name('student.register.post');
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

    Route::middleware('auth:student')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/portfolio', [StudentController::class, 'portfolio'])->name('student.portfolio');
    });
});
