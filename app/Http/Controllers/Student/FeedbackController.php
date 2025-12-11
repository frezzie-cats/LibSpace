<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Feedback;
use App\Models\Booking; // ADDED: Import the Booking Model
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
        // Define current date and time for comparison
        $today = Carbon::now()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // === NEW CONSTRAINT: CHECK FOR COMPLETED BOOKINGS ===
        // A booking is considered "completed" if its end time is in the past.
        $hasCompletedBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->where(function ($query) use ($today, $currentTime) {
                // Condition A: Booking was on a previous day
                $query->where('booking_date', '<', $today)
                      // Condition B: Booking is today, but the end_time is in the past
                      ->orWhere(function ($q) use ($today, $currentTime) {
                          $q->where('booking_date', $today)
                            ->where('end_time', '<', $currentTime);
                      });
            })
            ->exists();
            
        if (!$hasCompletedBookings) {
            return redirect()->back()->withInput()->with('error', 'You must have at least one completed facility booking before submitting feedback.');
        }
        // ===================================================

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