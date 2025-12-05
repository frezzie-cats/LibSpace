@extends('layouts.staff')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-light text-gray-800 mb-2">Welcome, {{ Auth::user()->name }}!</h1>
        <p class="text-lg text-gray-600">Staff Administration Panel for Library Space (LibSpace).</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
        
        <!-- Card 1: Facilities Management -->
        <div class="h-full">
            <div class="bg-white shadow-md rounded-lg overflow-hidden border-l-4 border-blue-600 h-full hover:shadow-xl transition duration-300">
                {{-- Use flex-col and justify-between on the inner p-5 to push the link to the bottom --}}
                <div class="p-5 flex flex-col h-full">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-xs font-semibold text-blue-600 uppercase mb-1">
                                Facilities Management
                            </div>
                            <div class="text-xl font-bold text-gray-800">Manage Rooms & Pads</div>
                        </div>
                        <div class="text-blue-500">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                    
                    <p class="text-gray-500 mb-6">View, add, edit, and update the status of all bookable facilities.</p>
                    
                    {{-- Button is pushed to the bottom right using mt-auto and text-right --}}
                    <div class="mt-auto text-center">
                        <a href="{{ route('staff.facilities.index') }}" class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition duration-150">Go to Facilities</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Booking Schedule (Active) -->
        <div class="h-full">
            <div class="bg-white shadow-md rounded-lg overflow-hidden border-l-4 border-indigo-600 h-full hover:shadow-xl transition duration-300">
                {{-- Use flex-col and justify-between on the inner p-5 to push the link to the bottom --}}
                <div class="p-5 flex flex-col h-full">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-xs font-semibold text-indigo-600 uppercase mb-1">
                                Booking Schedule
                            </div>
                            <div class="text-xl font-bold text-gray-800">Review Upcoming Bookings</div>
                        </div>
                        <div class="text-indigo-500">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                    
                    <p class="text-gray-500 mb-6">Monitor all student reservations, manage the daily schedule, and view booking history.</p>
                    
                    {{-- Button is pushed to the bottom right using mt-auto and text-right --}}
                    <div class="mt-auto text-center">
                        <a href="{{ route('staff.bookings.index') }}" class="inline-block px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition duration-150">
                            Go to Bookings Overview
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card 3: Feedback Review (NEW) -->
        <div class="h-full">
            <div class="bg-white shadow-md rounded-lg overflow-hidden border-l-4 border-green-600 h-full hover:shadow-xl transition duration-300">
                {{-- Use flex-col and justify-between on the inner p-5 to push the link to the bottom --}}
                <div class="p-5 flex flex-col h-full">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-xs font-semibold text-green-600 uppercase mb-1">
                                User Engagement
                            </div>
                            <div class="text-xl font-bold text-gray-800">Feedback Review</div>
                        </div>
                        <div class="text-green-500">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                    
                    <p class="text-gray-500 mb-6">View and manage user-submitted feedback, ratings, and issue reports.</p>
                    
                    {{-- Button is pushed to the bottom right using mt-auto and text-right --}}
                    <div class="mt-auto text-center">
                        <a href="{{ route('staff.feedbacks.index') }}" class="inline-block px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition duration-150">
                            Review Feedback
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection