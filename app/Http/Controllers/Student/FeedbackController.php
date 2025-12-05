<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Feedback; // ADDED: Import the new Feedback Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    public function index(): View
    {
        return view('student.feedbacks.index');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validate all required fields (subject, rating, and message)
        $validated = $request->validate([
            'subject' => ['required', 'string', 'in:discussion,center,pad,other'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            // 2. Save feedback to database
            Feedback::create([
                'user_id' => Auth::id(), // Automatically link to the logged-in user
                'subject' => $validated['subject'],
                'rating' => $validated['rating'],
                'message' => $validated['message'],
            ]);

            return redirect()->route('student.feedbacks.index')->with('success', 'Thank you! Your valuable feedback has been successfully submitted.');
        } catch (\Exception $e) {
            Log::error("Feedback submission error for user " . Auth::id() . ": " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'There was an error submitting your feedback. Please try again.');
        }
    }
}