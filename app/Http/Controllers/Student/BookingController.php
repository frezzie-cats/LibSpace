<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BookingController extends Controller
{
    // --- CONSTRAINTS (Set to 8:00 AM to 6:00 PM as requested) ---
    // Define the daily operating hours for all bookable facilities
    private $fixedOpeningTime = '08:00:00'; 
    private $fixedClosingTime = '18:00:00'; // 6 PM
    // ---------------------------------------------------

    /**
     * Display a listing of the user's bookings.
     */
    public function index()
    {
        /** @var User $user */ 
        $user = Auth::user(); 

        $bookings = $user->bookings()
                        ->with('facility')
                        ->orderBy('booking_date', 'desc')
                        ->orderBy('start_time', 'desc')
                        ->get();

        return view('student.bookings.index', compact('bookings'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        // Get today's date to enforce the "strictly today only" rule
        $today = Carbon::now()->format('Y-m-d');

        // 1. Validation: Accepts H:i:s (database) or H:i (frontend) formats
        $validatedData = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            // Ensures the booking date is strictly today
            'booking_date' => ['required', 'date', Rule::in([$today])], 
            'start_time' => ['required', 'date_format:H:i:s,H:i'],
            'end_time' => ['required', 'date_format:H:i:s,H:i', 'after:start_time'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $facility = Facility::find($validatedData['facility_id']);

        if (!$facility || $facility->status !== 'available') {
            return back()->with('error', 'This facility is currently not available for booking.');
        }

        // --- Prepare Carbon objects and standardized time components ---
        try {
            // Standardize raw input (which could be H:i or H:i:s) into H:i:s format for database and comparison
            $cleanStartTime = Carbon::parse($validatedData['start_time'])->format('H:i:s');
            $cleanEndTime = Carbon::parse($validatedData['end_time'])->format('H:i:s');
            
            // Create robust Carbon datetime objects for bounds/past checks
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $cleanStartTime);
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $cleanEndTime);
        } catch (\Exception $e) {
             Log::error('Critical Carbon Parsing Error in BookingController@store: ' . $e->getMessage());
             return back()->with('error', 'Critical input format error. Please check facility controller data.');
        }

        
        // 2. Time Bounds Check (Against defined operating hours)
        $openingTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $this->fixedOpeningTime);
        $closingTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $this->fixedClosingTime);

        // Check if the slot starts before the opening time or ends after the closing time
        if ($startDate->lessThan($openingTime) || $endDate->greaterThan($closingTime)) {
            return back()->with('error', "The requested time slot is outside the allowed booking hours (8:00 AM to 6:00 PM)."); 
        }

        // 3. Future Time Check (booking must start in the near future)
        // Add a 1-minute buffer to account for form submission time.
        if ($startDate->lessThan(Carbon::now()->addMinute(1))) {
            return back()->with('error', 'The selected start time is in the past or too soon. Please choose a future slot.');
        }
        
        // 4. Time Overlap and Capacity Check (Facility-wide limit)
        $conflictingBookingsCount = Booking::where('facility_id', $facility->id)
            ->where('booking_date', $today)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($cleanStartTime, $cleanEndTime) {
                // Check for overlap: existing_start < new_end AND existing_end > new_start
                $query->where('start_time', '<', $cleanEndTime)
                    ->where('end_time', '>', $cleanStartTime);
            })
            ->count();
            
        if ($conflictingBookingsCount >= $facility->capacity) {
            return back()->with('error', 'This facility is fully booked for the requested time slot. Please choose another time.');
        }

        // 5. User Booking Limit Check (Prevent user from booking two facilities at once)
        $userActiveBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->where('booking_date', $today)
            ->where(function ($query) use ($cleanStartTime, $cleanEndTime) {
                // Check if the current user already has an overlapping booking
                $query->where('start_time', '<', $cleanEndTime)
                    ->where('end_time', '>', $cleanStartTime);
            })
            ->count();
        
        if ($userActiveBookings > 0) {
            return back()->with('error', 'You already have an active booking during this time. You may only have one active booking per day.');
        }

        // 6. Create the Booking
        try {
            Booking::create([
                'user_id' => Auth::id(),
                'facility_id' => $facility->id,
                'booking_date' => $today,
                'start_time' => $cleanStartTime, // Using the standardized H:i:s format
                'end_time' => $cleanEndTime, // Using the standardized H:i:s format
                'notes' => $validatedData['notes'] ?? null,
                'status' => 'confirmed',
            ]);

            return redirect()->route('student.bookings.index')->with('success', 'Facility successfully booked for ' . $startDate->format('g:i A') . '!');
        } catch (\Exception $e) {
            Log::error('Booking failed: ' . $e->getMessage());
            return back()->with('error', 'Could not process the booking due to a system error. Please try again.');
        }
    }
    
    /**
     * Deletes a booking (Cancellation).
     * @param  \App\Models\Booking $booking
     */
    public function destroy(Booking $booking)
    {
        // 1. Authorization check: ensure the user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Time check: Only allow cancellation if the booking is in the future
        try {
            // Robustly combine date and time components for accurate comparison
            $cleanDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
            $cleanTime = Carbon::parse($booking->start_time)->format('H:i:s');
            
            // Create a Carbon object representing the moment the booking starts
            $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $cleanDate . ' ' . $cleanTime);
        } catch (\Exception $e) {
            Log::error("Carbon Parsing Error in BookingController@destroy for booking {$booking->id}: " . $e->getMessage());
            return back()->with('error', 'Error processing the booking time for cancellation check. Please contact support.');
        }
        
        if ($bookingDateTime->isPast()) {
            return back()->with('error', 'Cannot cancel a booking that has already started or passed.');
        }

        // 3. Delete the booking
        try {
            // Note: In a production system, you might change the status to 'cancelled' instead of deleting,
            // to keep a history of user actions.
            $booking->delete(); 
            return redirect()->route('student.bookings.index')->with('success', 'Booking successfully cancelled.');
        } catch (\Exception $e) {
            Log::error('Booking deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel the booking. Please try again.');
        }
    }
}