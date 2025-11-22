<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class BookingManagementController extends Controller
{
    /**
     * Display a list of all bookings across the system.
     * Staff view shows current and future bookings prominently.
     */
    public function index(): View
    {
        // Fetch all bookings. Eager load the related user (student) and facility.
        $bookings = Booking::with(['user', 'facility'])
                        ->orderBy('booking_date', 'desc')
                        ->orderBy('start_time', 'desc')
                        ->get();

        // Categorize bookings for display
        $todayBookings = $bookings->filter(fn ($b) => $b->booking_date === Carbon::today()->toDateString());
        $upcomingBookings = $bookings->filter(fn ($b) => $b->booking_date > Carbon::today()->toDateString());
        $pastBookings = $bookings->filter(fn ($b) => $b->booking_date < Carbon::today()->toDateString());

        return view('staff.bookings.index', compact(
            'todayBookings',
            'upcomingBookings',
            'pastBookings'
        ));
    }

    /**
     * Show a specific booking for review or details.
     */
    public function show(Booking $booking): View
    {
        // Eager load relationships needed for the details view
        $booking->load(['user', 'facility']);
        return view('staff.bookings.show', compact('booking'));
    }
}