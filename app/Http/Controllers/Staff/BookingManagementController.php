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
     * Staff view shows current and future bookings prominently in one 'Active' category.
     */
    public function index(): View
    {
        $todayDate = Carbon::today()->toDateString();

        // Fetch all bookings. Eager load the related user (student) and facility.
        // It's generally more efficient to use database queries (where) before collecting (get)
        // rather than fetching all and filtering in PHP (Collection filter).

        // Fetch ACTIVE Bookings (Today or Future)
        $activeBookings = Booking::with(['user', 'facility'])
            // Filter to include all bookings on or after the current day
            ->where('booking_date', '>=', $todayDate) 
            ->orderBy('booking_date', 'asc') // Upcoming first
            ->orderBy('start_time', 'asc')
            ->get();

        // Fetch PAST Bookings
        $pastBookings = Booking::with(['user', 'facility'])
            // Filter to include all bookings before the current day
            ->where('booking_date', '<', $todayDate)
            ->orderBy('booking_date', 'desc') // Recent past first
            ->orderBy('start_time', 'desc')
            ->get();

        // We now only pass 'activeBookings' (Today + Upcoming) and 'pastBookings'
        return view('staff.bookings.index', compact(
            'activeBookings',
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