<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BookingController extends Controller
{
    // --- CONSTRAINTS (Match FacilityController logic) ---
    private $fixedOpeningTime = '08:00:00';
    private $fixedClosingTime = '23:00:00'; // TEMPORARILY EXTENDED FOR TESTING
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

        // 1. Validation
        $validatedData = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'booking_date' => ['required', 'date', Rule::in([$today])], 
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s', 'after:start_time'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $facility = Facility::find($validatedData['facility_id']);

        if ($facility->status !== 'available') {
            return back()->with('error', 'This facility is currently not available for booking.');
        }

        // --- Prepare Carbon objects for checks ---
        try {
            // Clean components: Parse raw input, format it cleanly.
            $cleanDate = Carbon::parse($validatedData['booking_date'])->format('Y-m-d');
            $cleanStartTime = Carbon::parse($validatedData['start_time'])->format('H:i:s');
            $cleanEndTime = Carbon::parse($validatedData['end_time'])->format('H:i:s');
            
            // Create robust Carbon objects from clean components
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $cleanDate . ' ' . $cleanStartTime);
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $cleanDate . ' ' . $cleanEndTime);
        } catch (\Exception $e) {
             Log::error('Critical Carbon Parsing Error in BookingController@store: ' . $e->getMessage());
             return back()->with('error', 'Critical input format error. Please check facility controller data.');
        }

        
        // 3. Time Bounds Check 
        $openingTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $this->fixedOpeningTime);
        $closingTime = Carbon::createFromFormat('Y-m-d H:i:s', $today . ' ' . $this->fixedClosingTime);

        if ($startDate->lessThan($openingTime) || $endDate->greaterThan($closingTime)) {
            return back()->with('error', 'The requested time slot is outside the allowed booking hours (8:00 AM - 11:00 PM).');
        }

        // 4. Future Time Check
        if ($startDate->lessThan(Carbon::now())) {
            return back()->with('error', 'The selected start time is in the past. Please choose a future slot.');
        }
        
        // 5. Time Overlap and Capacity Check
        $conflictingBookingsCount = Booking::where('facility_id', $facility->id)
            ->where('booking_date', $validatedData['booking_date'])
            ->where('status', 'confirmed')
            ->where(function ($query) use ($validatedData) {
                // Check for overlap: existing_start < new_end AND existing_end > new_start
                $query->where('start_time', '<', $validatedData['end_time'])
                      ->where('end_time', '>', $validatedData['start_time']);
            })
            ->count();
            
        if ($conflictingBookingsCount >= $facility->capacity) {
            return back()->with('error', 'This facility is fully booked for the requested time slot. Please choose another time.');
        }

        // 6. User Booking Limit Check (Prevent user from booking two facilities at once)
        $userActiveBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->where('booking_date', $validatedData['booking_date'])
            ->where(function ($query) use ($validatedData) {
                // Check if the current user already has an overlapping booking
                $query->where('start_time', '<', $validatedData['end_time'])
                      ->where('end_time', '>', $validatedData['start_time']);
            })
            ->count();
        
        if ($userActiveBookings > 0) {
            return back()->with('error', 'You already have an active booking during this time. You may only have one active booking per day.');
        }

        // 7. Create the Booking
        try {
            Booking::create([
                'user_id' => Auth::id(),
                'facility_id' => $facility->id,
                'booking_date' => $validatedData['booking_date'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'notes' => $validatedData['notes'] ?? null,
                'status' => 'confirmed',
            ]);

            // FIX: Change redirect route from 'student.dashboard' to 'student.bookings.index' 
            // as the latter is a working route (proven by destroy method) and is more relevant.
            return redirect()->route('student.bookings.index')->with('success', 'Facility successfully booked for ' . $startDate->format('g:i A') . '!');
        } catch (\Exception $e) {
            // This catch block should now only execute for a true database write failure.
            Log::error('Booking failed: ' . $e->getMessage());
            return back()->with('error', 'Could not process the booking due to a system error. Please try again.');
        }
    }
    
    /**
     * Deletes a booking (Cancellation).
     * @param  \App\Models\Booking $booking
     */
    public function destroy(Booking $booking)
    {
        // 1. Authorization check: ensure the user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Time check: Only allow cancellation if the booking is in the future
        // FIX: Clean the components before combining for Carbon::createFromFormat
        try {
            // Clean the date part (ensures only Y-m-d)
            $cleanDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
            // Clean the time part (ensures only H:i:s)
            $cleanTime = Carbon::parse($booking->start_time)->format('H:i:s');
            
            // Combine the clean components into a single, valid datetime string
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
            $booking->delete();
            return redirect()->route('student.bookings.index')->with('success', 'Booking successfully cancelled.');
        } catch (\Exception $e) {
            Log::error('Booking deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel the booking. Please try again.');
        }
    }
}