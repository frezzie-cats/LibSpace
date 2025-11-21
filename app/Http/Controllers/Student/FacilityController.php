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

class FacilityController extends Controller
{
    // The working hours and booking duration rules
    private $intervalMinutes = 60;
    private $startTime = '09:00:00';
    private $endTime = '17:00:00';
    private $maxBookingDurationHours = 2;

    /**
     * Display a listing of available facilities.
     * * @return View
     */
    public function index(): View
    {
        $facilities = Facility::where('status', 'available')->orderBy('name')->get();
        return view('student.facilities.index', compact('facilities'));
    }

    /**
     * Show the facility details and available booking slots.
     * * @param Facility $facility
     * @param Request $request
     * @return View|RedirectResponse // <-- MODIFIED: Allow View or RedirectResponse
     */
    public function show(Facility $facility, Request $request): View|RedirectResponse
    {
        // 1. Get and validate the requested date
        $bookingDate = $request->input('date', Carbon::now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($bookingDate);
        $today = Carbon::now()->startOfDay();

        // Prevent booking in the past
        if ($selectedDate->isBefore($today)) {
            return redirect()->route('student.facilities.show', $facility)->with('error', 'You cannot view availability for a past date.');
        }

        // 2. Initial check on facility status
        if ($facility->status !== 'available') {
            return redirect()->route('student.facilities.index')->with('error', 'This facility is currently unavailable for booking.');
        }

        // 3. Generate all potential slots for the day
        $allSlots = $this->generatePossibleSlots();
        
        // 4. Fetch confirmed bookings for the selected date
        $confirmedBookings = $facility->bookings()
            ->where('booking_date', $bookingDate)
            ->where('status', 'confirmed')
            ->get();

        // 5. Determine available slots
        $availableSlots = [];
        $currentTime = Carbon::now();
        $isToday = $selectedDate->isSameDay($currentTime);

        foreach ($allSlots as $slot) {
            $slotStart = Carbon::parse($bookingDate . ' ' . $slot['start_time']);
            
            $isFuture = true;
            if ($isToday && $slotStart->lessThanOrEqualTo($currentTime)) {
                // For today, only show slots that haven't started yet
                $isFuture = false;
            }

            // Count current confirmed bookings that overlap with this slot
            $currentBookings = $confirmedBookings->filter(function ($booking) use ($slot) {
                // Overlap check: start < B_end AND end > B_start
                return $slot['start_time'] < $booking->end_time && $slot['end_time'] > $booking->start_time;
            })->count();

            // Only add the slot if capacity hasn't been reached
            if ($currentBookings < $facility->capacity) {
                $availableSlots[] = array_merge($slot, [
                    'current_bookings' => $currentBookings,
                    'is_future' => $isFuture,
                ]);
            }
        }

        return view('student.facilities.show', [
            'facility' => $facility,
            'bookingDate' => $bookingDate,
            'selectedDate' => $selectedDate,
            'availableSlots' => $availableSlots,
        ]);
    }

    /**
     * Generates all possible 1-hour slots based on facility hours.
     */
    private function generatePossibleSlots(): array
    {
        $slots = [];
        $current = Carbon::parse($this->startTime);
        $end = Carbon::parse($this->endTime);

        while ($current->lessThan($end)) {
            $next = $current->copy()->addHours($this->maxBookingDurationHours); 
            
            // Adjust to the max end time if it goes over
            if ($next->greaterThan($end)) {
                $next = $end;
            }

            // Only generate slots that are at least one hour long
            if ($next->diffInMinutes($current) >= 60) {
                 $slots[] = [
                    'start_time' => $current->format('H:i:s'),
                    'end_time' => $next->format('H:i:s'),
                    'label' => $current->format('g:i A') . ' - ' . $next->format('g:i A'),
                ];
            }
            
            // Move to the next interval (which is 1 hour in our setup for continuous booking)
            $current->addMinutes($this->intervalMinutes);
        }

        return $slots;
    }
}