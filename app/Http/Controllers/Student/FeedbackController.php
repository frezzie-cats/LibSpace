<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View; // Keep this
use Illuminate\Http\RedirectResponse; // <-- ADDED: For type-hinting redirects
use Carbon\Carbon;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('student.feedbacks.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // Save feedback to database or send email — optional
        // Feedback::create($request->all());

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
}
