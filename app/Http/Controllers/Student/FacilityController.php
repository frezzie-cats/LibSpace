<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class FacilityController extends Controller
{
    // The maximum booking duration rule is strictly 1 hour.
    private $maxBookingDurationHours = 1; 

    // Operating hours definition (8:00 AM to 6:00 PM)
    private $fixedOpeningTime = '08:00:00'; 
    private $fixedClosingTime = '18:00:00'; 
    // ------------------------

    /**
     * Display a listing of available facilities.
     * @return View
     */
    public function index(): View
    {
        $facilities = Facility::where('status', 'available')->orderBy('name')->get(); 
        return view('student.facilities.index', compact('facilities'));
    }

    /**
     * Show the facility details and available booking slots.
     * @param Facility $facility
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function show(Facility $facility, Request $request): View|RedirectResponse
    {
        // 1. HARDCODE BOOKING DATE TO TODAY ONLY
        $bookingDate = Carbon::now()->format('Y-m-d');
        $selectedDate = Carbon::parse($bookingDate);

        // Prevent booking if the facility is closed for the day (RESTORED)
        $now = Carbon::now();
        $closingTimeToday = Carbon::parse($bookingDate . ' ' . $this->fixedClosingTime);
        
        // If current time is past or exactly the closing time (6:00 PM)
        if ($now->greaterThanOrEqualTo($closingTimeToday)) {
            return redirect()->route('student.facilities.index', ['type' => $facility->type])
                ->with('error', "Booking is closed for today. Facilities close at 6:00 PM."); 
        }
        
        // 2. Initial check on facility status
        if ($facility->status !== 'available') {
            return redirect()->route('student.facilities.index')->with('error', 'This facility is currently unavailable for booking.');
        }

        // 3. Fetch confirmed bookings for the selected date (Today)
        $confirmedBookings = $facility->bookings()
            ->where('booking_date', $bookingDate)
            ->where('status', 'confirmed')
            ->get();

        // 4. Generate all potential slots and determine availability
        $availableSlots = $this->generateAvailableSlots(
            $facility,
            $bookingDate,
            $confirmedBookings
        );

        return view('student.facilities.show', [
            'facility' => $facility,
            'bookingDate' => $bookingDate, 
            'selectedDate' => $selectedDate,
            'availableSlots' => $availableSlots,
            'maxDuration' => $this->maxBookingDurationHours,
        ]);
    }

    /**
     * Generates fixed 1-hour slots based on operating hours (8 AM - 6 PM).
     * @param Facility $facility
     * @param string $bookingDate
     * @param \Illuminate\Support\Collection $confirmedBookings
     * @return array
     */
    private function generateAvailableSlots(Facility $facility, string $bookingDate, $confirmedBookings): array
    {
        $slots = [];
        
        // Use the defined operating hours
        $current = Carbon::parse($this->fixedOpeningTime);
        $end = Carbon::parse($this->fixedClosingTime);
        
        $currentTime = Carbon::now();
        $isToday = Carbon::parse($bookingDate)->isSameDay($currentTime);
        
        // We will generate fixed 1-hour slots
        while ($current->lt($end)) {
            $slotStart = $current->copy();
            $slotEnd = $current->copy()->addHour(); // The booking is strictly 1 hour

            // Check if the 1-hour slot goes past the closing boundary
            if ($slotEnd->gt($end)) {
                break;
            }

            // Check if the slot is in the past (for front-end display disabling)
            // Use a 1-minute buffer for a grace period
            $isFuture = true;
            if ($isToday && $slotStart->lt($currentTime->copy()->addMinute(1))) {
                // For today, if the slot start time is in the past, mark it as unavailable/past
                $isFuture = false;
            }

            // Check how many confirmed bookings overlap with this specific 1-hour slot
            $currentBookings = $confirmedBookings->filter(function ($booking) use ($slotStart, $slotEnd) {
                // Overlap condition: Booking start time is before slot end time AND Booking end time is after slot start time.
                return Carbon::parse($booking->start_time)->lt($slotEnd) && 
                         Carbon::parse($booking->end_time)->gt($slotStart);
            })->count();

            // A slot is available if its capacity hasn't been reached and it's not in the past
            $isAvailable = $isFuture && ($currentBookings < $facility->capacity);

            $slots[] = [
                'start_time' => $slotStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'label' => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                'is_available' => $isAvailable,
                'current_bookings' => $currentBookings,
            ];

            // Move to the start of the next 1-hour slot
            $current->addHour();
        }

        return $slots;
    }
}