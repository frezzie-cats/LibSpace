@extends('layouts.student')
@section('title', 'Book: ' . $facility->name)

@section('content')

@php
    // --- Dynamic Color Mapping ---
    $facilityColors = [
        'room' => 'border-blue-500',
        'pad' => 'border-indigo-500', // Using indigo for Nap Pad
        'venue' => 'border-orange-500',
    ];
    
    // Get the color class, defaulting to a gray if type is unexpected
    $borderColorClass = $facilityColors[$facility->type] ?? 'border-gray-500';
@endphp

    <a href="{{ route('student.facilities.index', ['type' => $facility->type]) }}" class="text-green-600 hover:text-green-700 text-sm mb-4 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to {{ ucfirst($facility->type) }} Overview
    </a>

    <div class="max-w-4xl mx-auto">
        
        {{-- =================================== --}}
        {{-- I. FLASH MESSAGES (SUCCESS ONLY) --}}
        {{-- =================================== --}}

        @if (session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        {{-- ERROR MESSAGES (session('error') and $errors->any()) are assumed to be handled by layouts.student --}}

        {{-- =================================== --}}
        {{-- II. FACILITY DETAILS CARD --}}
        {{-- =================================== --}}

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8 border-t-4 {{ $borderColorClass }}">
            <h3 class="text-2xl font-bold mb-2 text-gray-900">{{ $facility->name }}</h3>
            <p class="text-gray-600 mb-1">Type: {{ ucfirst($facility->type) }}</p>
            <p class="text-gray-600 mb-1">Capacity: {{ $facility->capacity }} simultaneous bookings</p>
            <p class="text-gray-600 mb-4">Status: <span class="{{ $facility->status === 'available' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">{{ ucfirst($facility->status) }}</span></p>
            
            <p class="text-sm text-gray-500 mt-4">
                Booking is strictly for today ({{ \Carbon\Carbon::parse($bookingDate)->format('F d, Y') }}) only, strictly within the facility's operating hours (8:00 AM to 6:00 PM).
            </p>
        </div>

        {{-- =================================== --}}
        {{-- III. TIME SLOT SELECTION --}}
        {{-- =================================== --}}
        
        <h3 class="text-xl font-semibold mb-4 text-gray-700 mt-8">Available Time Slots (Today)</h3>
        
        <p class="text-sm text-yellow-600 bg-yellow-100 p-3 rounded-lg mb-6 border-l-4 border-yellow-500">
            ⚠️ Note on Slot Status: Slots are marked as 'Passed' if the current server time is after the slot's start time. 
            Slots are marked 'Full' if the number of overlapping, confirmed bookings reaches the facility's capacity ({{ $facility->capacity }}).
        </p>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="time-slot-grid">
            
            @forelse ($availableSlots as $slot)
                @php
                    $isAvailable = $slot['is_available'];
                    $currentBookings = $slot['current_bookings'];

                    // Determine Status Text and Class based on controller's pre-calculated logic
                    $buttonClass = 'bg-gray-200 text-gray-500 cursor-not-allowed';
                    $statusText = 'Unavailable';
                    $isPast = false;

                    // Check if the slot start time is in the past (based on server time now)
                    if ($isAvailable) {
                        $buttonClass = 'bg-green-500 text-white cursor-pointer hover:bg-green-600 transition duration-150 ease-in-out';
                        $statusText = 'Available';
                    } elseif (Carbon\Carbon::createFromFormat('H:i:s', $slot['start_time'])->lessThan(Carbon\Carbon::now()->setTimezone('Asia/Manila')->startOfMinute())) {
                        // Check if the time slot has passed (based on the current time)
                        $buttonClass = 'bg-gray-100 text-gray-400 cursor-not-allowed';
                        $statusText = 'Passed';
                        $isPast = true;
                    } elseif ($currentBookings >= $facility->capacity) {
                        $buttonClass = 'bg-red-500 text-white cursor-not-allowed hover:bg-red-600';
                        $statusText = 'Full';
                    }

                    // 4. Merge all classes into one string for clean HTML
                    $baseClasses = 'p-4 rounded-lg shadow-md font-medium text-sm text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-20 flex flex-col justify-center items-center';
                    $finalClasses = $baseClasses . ' ' . $buttonClass;
                    
                    if ($statusText === 'Available') {
                        $finalClasses .= ' book-slot-btn'; // Add JS hook class
                    }
                @endphp

                <button 
                    @if ($statusText !== 'Available') 
                        disabled
                    @endif
                    {{-- Data attributes store the values for the JavaScript listener --}}
                    data-start-time="{{ $slot['start_time'] }}"
                    data-end-time="{{ $slot['end_time'] }}"
                    data-time-display="{{ $slot['label'] }}"
                    class="{{ $finalClasses }}"
                >
                    <span class="text-base">{{ \Carbon\Carbon::parse($slot['start_time'])->format('g:i A') }}</span>
                    <span class="text-xs mt-1">{{ $statusText }}</span>
                    @if ($statusText === 'Available')
                        <span class="text-xs font-normal text-white/80">({{ $currentBookings }}/{{ $facility->capacity }} booked)</span>
                    @endif
                </button>
            @empty
                <div class="col-span-full p-6 bg-gray-50 rounded-lg text-center text-gray-500">
                    No available slots for today.
                </div>
            @endforelse
        </div>

    </div>

    {{-- =================================== --}}
    {{-- IV. BOOKING MODAL STRUCTURE --}}
    {{-- =================================== --}}

    <div id="booking-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Confirm Your Booking
            </h3>
            <div class="mt-2">
                <p class="text-sm text-gray-700">
                    You are booking {{ $facility->name }} for the slot: <span id="slot-display" class="font-semibold text-green-600"></span>.
                </p>

                <form id="booking-form" method="POST" action="{{ route('student.bookings.store') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="facility_id" value="{{ $facility->id }}">
                    <input type="hidden" name="booking_date" value="{{ $bookingDate }}">
                    <input type="hidden" name="start_time" id="modal-start-time">
                    <input type="hidden" name="end_time" id="modal-end-time">
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Purpose of booking, e.g., 'Group study session'"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="close-modal-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-150">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-700 transition duration-150">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- =================================== --}}
    {{-- V. JAVASCRIPT FOR MODAL & EVENTS --}}
    {{-- =================================== --}}

    <script>
        // Helper function to show/hide the modal
        function toggleModal(show) {
            const modal = document.getElementById('booking-modal');
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // The function called when a slot button is clicked
        function showBookingModal(startTime, endTime, timeDisplay) {
            // Update the form fields
            document.getElementById('modal-start-time').value = startTime;
            document.getElementById('modal-end-time').value = endTime;
            document.getElementById('slot-display').textContent = timeDisplay;
            
            // Show the modal
            toggleModal(true);
        }

        // Attach event listeners after the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Attach click handler to all available slot buttons
            document.querySelectorAll('.book-slot-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const start = this.getAttribute('data-start-time');
                    const end = this.getAttribute('data-end-time');
                    const display = this.getAttribute('data-time-display');
                    // Call the modal function using the values stored in data attributes
                    showBookingModal(start, end, display);
                });
            });

            // 2. Event listener for the Cancel button in the modal
            document.getElementById('close-modal-btn').addEventListener('click', () => toggleModal(false));

            // 3. Simple way to handle clicks outside the modal to close it
            document.getElementById('booking-modal').addEventListener('click', function(event) {
                if (event.target === this) {
                    toggleModal(false);
                }
            });

            // 4. Ensure the modal can close with ESC key
            document.addEventListener('keydown', function(event) {
                const modal = document.getElementById('booking-modal');
                if (event.key === 'Escape' && modal.classList.contains('flex')) {
                    toggleModal(false);
                }
            });
        });
    </script>
@endsection