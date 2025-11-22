@extends('layouts.staff')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-light text-gray-800 mb-2">Welcome, {{ Auth::user()->name }}!</h1>
        <p class="text-lg text-gray-600">Staff Administration Panel for Library Space (LibSpace).</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
        <!-- Card 1: Facilities Management -->
        <div class="h-full">
            <div class="bg-white shadow-md rounded-lg overflow-hidden border-l-4 border-blue-600 h-full">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-xs font-semibold text-blue-600 uppercase mb-1">
                                Facilities Management
                            </div>
                            <div class="text-xl font-bold text-gray-800">Manage Rooms & Pads</div>
                        </div>
                        <div class="text-gray-400">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-gray-500 mt-4 mb-4">View, add, edit, and update the status of all bookable facilities.</p>
                    <a href="{{ route('staff.facilities.index') }}" class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-150">Go to Facilities</a>
                </div>
            </div>
        </div>

        <!-- Card 2: Booking Schedule (Active) -->
        <div class="h-full">
            <div class="bg-white shadow-md rounded-lg overflow-hidden border-l-4 border-indigo-600 h-full">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-xs font-semibold text-indigo-600 uppercase mb-1">
                                Booking Schedule
                            </div>
                            <div class="text-xl font-bold text-gray-800">Review Upcoming Bookings</div>
                        </div>
                        <div class="text-gray-400">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-gray-500 mt-4 mb-4">Monitor all student reservations, manage the daily schedule, and view booking history.</p>
                    
                    {{-- UPDATED: Active link using the defined route --}}
                    <a href="{{ route('staff.bookings.index') }}" class="inline-block px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition duration-150">
                        Go to Bookings Overview
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Placeholder Card (Optional, for visual balance) -->
        <div class="h-full hidden lg:block">
            <div class="bg-white shadow-md rounded-lg overflow-hidden border-l-4 border-gray-400 h-full">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">
                                System Status
                            </div>
                            <div class="text-xl font-bold text-gray-800">All Systems Operational</div>
                        </div>
                        <div class="text-gray-400">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-gray-500 mt-4 mb-4">System is running smoothly. Check audit logs for recent staff actions.</p>
                    <a href="#" class="inline-block px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600 transition duration-150">View Logs</a>
                </div>
            </div>
        </div>
    </div>
@endsection