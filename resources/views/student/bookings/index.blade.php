@extends('layouts.student')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">My Current & Past Bookings</h1>
        <p class="text-gray-600">Review your schedule and manage your upcoming facility reservations.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @php
        use Carbon\Carbon;
        // Separate bookings into upcoming and history for clearer display
        $upcomingBookings = $bookings->filter(function ($booking) {
            $dateTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
            return $dateTime->isFuture();
        });
        
        $pastBookings = $bookings->filter(function ($booking) {
            $dateTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
            return !$dateTime->isFuture();
        });
    @endphp

    <!-- Upcoming Bookings Section -->
    <div class="mb-10">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Upcoming Reservations ({{ $upcomingBookings->count() }})</h2>

        @if ($upcomingBookings->isEmpty())
            <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-indigo-500">
                <p class="text-gray-600">You have no upcoming confirmed bookings. <a href="{{ route('student.facilities.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Book a facility now!</a></p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($upcomingBookings as $booking)
                    <div class="bg-white shadow-lg rounded-xl p-5 border-l-4 border-green-500 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="flex-grow mb-3 md:mb-0">
                            <p class="text-lg font-bold text-gray-900">{{ $booking->facility->name }}</p>
                            <p class="text-sm text-gray-600">
                                <i class="far fa-calendar-alt mr-1"></i> 
                                {{ Carbon::parse($booking->booking_date)->format('D, M j, Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="far fa-clock mr-1"></i> 
                                {{ Carbon::parse($booking->start_time)->format('h:i A') }} - {{ Carbon::parse($booking->end_time)->format('h:i A') }}
                            </p>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                CONFIRMED
                            </span>
                            
                            <!-- Cancellation Form -->
                            <form action="{{ route('student.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition duration-150 shadow-md">
                                    <i class="fas fa-times-circle mr-1"></i> Cancel
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Booking History Section -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Booking History ({{ $pastBookings->count() }})</h2>

        @if ($pastBookings->isEmpty())
            <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-gray-500">
                <p class="text-gray-600">You have no past booking history.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($pastBookings as $booking)
                    <div class="bg-gray-50 shadow-sm rounded-lg p-4 border-l-4 border-gray-400 flex justify-between items-center opacity-80">
                        <div>
                            <p class="text-base font-semibold text-gray-700">{{ $booking->facility->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ Carbon::parse($booking->booking_date)->format('M j, Y') }} | 
                                {{ Carbon::parse($booking->start_time)->format('h:i A') }} - {{ Carbon::parse($booking->end_time)->format('h:i A') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-200 text-gray-600">
                            COMPLETED
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection