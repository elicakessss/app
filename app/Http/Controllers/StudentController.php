<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Handles student dashboard and portfolio functionality.
 */
class StudentController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function dashboard(): View
    {
        $student = Auth::guard('student')->user();
        return view('student.dashboard', compact('student'));
    }

    /**
     * Display the student portfolio.
     */
    public function portfolio(): View
    {
        $student = Auth::guard('student')->user();
        return view('student.portfolio', compact('student'));
    }
}
