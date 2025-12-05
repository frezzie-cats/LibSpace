@extends('layouts.staff')

@section('title', 'Booking Overview')

@section('content')

<div class="space-y-8 staff-content">
<h1 class="text-3xl font-bold text-gray-800">System-Wide Booking Overview</h1>
<p class="text-gray-600">Review, monitor, and manage all facility reservations. All non-past reservations are shown under Active Bookings.</p>

<!-- Status Indicators (Now 2 Columns) -->
{{-- NOTE: Assumes the controller now passes a single combined collection named $activeBookings --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center">
    <div class="p-4 bg-blue-100 rounded-lg shadow-md border-l-4 border-blue-500">
        <div class="text-sm font-semibold text-blue-700 uppercase tracking-wider">Active Bookings</div>
        <div class="text-2xl font-bold text-blue-900">{{ $activeBookings->count() }}</div>
    </div>
    
    <div class="p-4 bg-gray-100 rounded-lg shadow-md border-l-4 border-gray-500">
        <div class="text-sm font-semibold text-gray-700 uppercase tracking-wider">History (Past)</div>
        <div class="text-2xl font-bold text-gray-900">{{ $pastBookings->count() }}</div>
    </div>
</div>

<!-- Tabbed Booking List -->
<div x-data="{ activeTab: 'active' }" class="p-6 bg-white rounded-xl shadow-lg">

    <!-- Tab Buttons (Uses Alpine.js for switching views) -->
    <div class="flex border-b mb-6">
        {{-- Renamed to Active Tab (Combines Today and Upcoming) --}}
        <button 
            @click="activeTab = 'active'"
            :class="{'border-indigo-500 text-indigo-600': activeTab === 'active', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'active'}"
            class="py-2 px-4 text-sm font-medium border-b-2 transition duration-150 ease-in-out focus:outline-none"
        >
            Active ({{ $activeBookings->count() }})
        </button>
        
        {{-- Removed Upcoming Tab Button --}}

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
        <!-- Active Bookings (Today + Upcoming) -->
        <div x-show="activeTab === 'active'">
            @if($activeBookings->isEmpty())
                <div class="p-4 bg-blue-50 rounded-lg text-blue-700 border border-blue-200 italic">
                    No active bookings currently scheduled (Today or Future).
                </div>
            @else
                {{-- Includes the table partial, passing the combined collection --}}
                @include('staff.bookings.partials.booking-table', ['bookings' => $activeBookings])
            @endif
        </div>

        {{-- Removed Upcoming Bookings Block --}}

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