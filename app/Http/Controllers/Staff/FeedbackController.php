<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource (all feedback).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all feedbacks, eager-load the related student (User) information.
        // We use 'student' which is defined as an alias for 'user' in the Feedback model.
        $feedbacks = Feedback::with('student')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return view('staff.feedbacks.index', compact('feedbacks'));
    }

    /**
     * Update the status of a specific feedback item via AJAX/PATCH request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            // Ensure the status is one of the allowed enum values defined in the database migration
            'status' => ['required', Rule::in(['new', 'reviewed', 'resolved', 'ignored'])],
        ]);

        $feedback->update($validated);

        return back()->with('success', 'Feedback status updated successfully to ' . $validated['status'] . '.');
    }
}