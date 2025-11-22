@extends('layouts.staff')

@section('title', 'Booking Overview')

@section('content')
<div class="space-y-8 staff-content">
    <h1 class="text-3xl font-bold text-gray-800">System-Wide Booking Overview</h1>
    <p class="text-gray-600">Review, monitor, and manage all facility reservations across the library system.</p>

    <!-- Status Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
        <div class="p-4 bg-blue-100 rounded-lg shadow-md border-l-4 border-blue-500">
            <div class="text-sm font-semibold text-blue-700 uppercase tracking-wider">Today's Bookings</div>
            <div class="text-2xl font-bold text-blue-900">{{ $todayBookings->count() }}</div>
        </div>
        <div class="p-4 bg-green-100 rounded-lg shadow-md border-l-4 border-green-500">
            <div class="text-sm font-semibold text-green-700 uppercase tracking-wider">Upcoming</div>
            <div class="text-2xl font-bold text-green-900">{{ $upcomingBookings->count() }}</div>
        </div>
        <div class="p-4 bg-gray-100 rounded-lg shadow-md border-l-4 border-gray-500">
            <div class="text-sm font-semibold text-gray-700 uppercase tracking-wider">History (Past)</div>
            <div class="text-2xl font-bold text-gray-900">{{ $pastBookings->count() }}</div>
        </div>
    </div>

    <!-- Tabbed Booking List -->
    <div x-data="{ activeTab: 'today' }" class="p-6 bg-white rounded-xl shadow-lg">

        <!-- Tab Buttons (Uses Alpine.js for switching views) -->
        <div class="flex border-b mb-6">
            <button 
                @click="activeTab = 'today'"
                :class="{'border-indigo-500 text-indigo-600': activeTab === 'today', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'today'}"
                class="py-2 px-4 text-sm font-medium border-b-2 transition duration-150 ease-in-out focus:outline-none"
            >
                Today's ({{ $todayBookings->count() }})
            </button>
            <button 
                @click="activeTab = 'upcoming'"
                :class="{'border-indigo-500 text-indigo-600': activeTab === 'upcoming', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'upcoming'}"
                class="py-2 px-4 text-sm font-medium border-b-2 transition duration-150 ease-in-out focus:outline-none"
            >
                Upcoming ({{ $upcomingBookings->count() }})
            </button>
            <button 
                @click="activeTab = 'past'"
                :class="{'border-indigo-500 text-indigo-600': activeTab === 'past', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'past'}"
                class="py-2 px-4 text-sm font-medium border-b-2 transition duration-150 ease-in-out focus:outline-none"
            >
                History ({{ $pastBookings->count() }})
            </button>
        </div>

        <!-- Tab Content -->
        <div>
            <!-- Today's Bookings -->
            <div x-show="activeTab === 'today'">
                @if($todayBookings->isEmpty())
                    <div class="p-4 bg-blue-50 rounded-lg text-blue-700 border border-blue-200 italic">
                        No confirmed bookings scheduled for today.
                    </div>
                @else
                    {{-- Includes the table partial, passing the collection --}}
                    @include('staff.bookings.partials.booking-table', ['bookings' => $todayBookings])
                @endif
            </div>

            <!-- Upcoming Bookings -->
            <div x-show="activeTab === 'upcoming'">
                @if($upcomingBookings->isEmpty())
                    <div class="p-4 bg-green-50 rounded-lg text-green-700 border border-green-200 italic">
                        No future bookings currently scheduled.
                    </div>
                @else
                    @include('staff.bookings.partials.booking-table', ['bookings' => $upcomingBookings])
                @endif
            </div>

            <!-- Past Bookings -->
            <div x-show="activeTab === 'past'">
                @if($pastBookings->isEmpty())
                    <div class="p-4 bg-gray-50 rounded-lg text-gray-700 border border-gray-200 italic">
                        No past booking history found.
                    </div>
                @else
                    @include('staff.bookings.partials.booking-table', ['bookings' => $pastBookings])
                @endif
            </div>
        </div>
    </div>
</div>
@endsection