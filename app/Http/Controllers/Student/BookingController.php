<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- ADDED to fix Undefined type 'Log'
use Illuminate\Support\Facades\Auth; // <-- ADDED to fix Undefined method 'user' and 'id'
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BookingController extends Controller
{
    // The maximum number of hours a student can book consecutively (must match FacilityController logic)
    private $maxBookingDurationHours = 2; 

    // The earliest and latest time for bookings
    private $startTime = '09:00:00';
    private $endTime = '17:00:00';

    /**
     * Display a listing of the user's bookings.
     */
    public function index()
    {
        // Use Auth::user() instead of auth()->user() for better IDE resolution
        /** @var User $user */ 
        $user = Auth::user(); 

        // Fetch all bookings for the authenticated user, eager loading the related facility
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
        // 1. Validate incoming data
        $data = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s', 'after:start_time'],
        ]);

        $facility = Facility::find($data['facility_id']);

        // 2. Basic Availability Check
        if ($facility->status !== 'available') {
            return back()->with('error', 'This facility is currently not available for booking.');
        }

        $startDate = Carbon::parse($data['booking_date'] . ' ' . $data['start_time']);
        $endDate = Carbon::parse($data['booking_date'] . ' ' . $data['end_time']);
        $duration = $endDate->diffInHours($startDate);
        
        // 3. Max Duration Check
        if ($duration > $this->maxBookingDurationHours || $duration <= 0) {
            return back()->with('error', "Bookings must be between 1 and {$this->maxBookingDurationHours} hours long.");
        }

        // 4. Time Overlap and Capacity Check
        $conflictingBookingsCount = Booking::where('facility_id', $facility->id)
            ->where('booking_date', $data['booking_date'])
            ->where('status', 'confirmed')
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>', $data['start_time']);
            })
            ->count();
            
        if ($conflictingBookingsCount >= $facility->capacity) {
            return back()->with('error', 'This facility is fully booked for the requested time slot. Please choose another time.');
        }

        // 5. User Booking Limit Check (Prevent user from booking two facilities at once)
        $userActiveBookings = Booking::where('user_id', Auth::id()) // <-- Using Auth::id()
            ->where('status', 'confirmed')
            ->where('booking_date', $data['booking_date'])
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>', $data['start_time']);
            })
            ->count();
        
        if ($userActiveBookings > 0) {
            return back()->with('error', 'You already have an active booking during this time. Please finish or cancel your existing booking first.');
        }

        // 6. Create the Booking
        try {
            Booking::create([
                'user_id' => Auth::id(), // <-- Using Auth::id()
                'facility_id' => $facility->id,
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'status' => 'confirmed',
            ]);

            return redirect()->route('student.bookings.index')->with('success', 'Facility successfully booked!');
        } catch (\Exception $e) {
            Log::error('Booking failed: ' . $e->getMessage()); // <-- Using Log::error()
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
        if ($booking->user_id !== Auth::id()) { // <-- Using Auth::id()
            abort(403, 'Unauthorized action.');
        }

        // 2. Time check: Only allow cancellation if the booking is in the future
        $bookingDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
        if ($bookingDateTime->isPast()) {
            return back()->with('error', 'Cannot cancel a booking that has already started or passed.');
        }

        // 3. Delete the booking
        $booking->delete();

        return redirect()->route('student.bookings.index')->with('success', 'Booking successfully cancelled.');
    }
}