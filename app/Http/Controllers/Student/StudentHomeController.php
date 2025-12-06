<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentHomeController extends Controller
{
    /**
     * Show the student's main dashboard/home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // This is the line that attempts to load the view.
        // If this still fails after creating the file and clearing caches, 
        // the path or filename is incorrect.
        return view('student.home'); 
    }
}