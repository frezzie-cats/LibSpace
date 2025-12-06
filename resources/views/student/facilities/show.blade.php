<!-- 
    NOTE: This assumes the following variables are passed from the controller:
    $facility: The Facility model instance.
    $bookings: A collection of existing bookings for the selected date (filtered by facility and date).
    $today: The current date string ('Y-m-d').
-->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Book: {{ $facility->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Facility Card -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8 border-t-4 border-indigo-500">
                <h3 class="text-2xl font-bold mb-2 text-indigo-700">{{ $facility->name }}</h3>
                <p class="text-gray-600 mb-1">Type: {{ ucfirst($facility->type) }}</p>
                <p class="text-gray-600 mb-1">Capacity: {{ $facility->capacity }} simultaneous bookings</p>
                <p class="text-gray-600 mb-4">Status: <span class="{{ $facility->status === 'available' ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">{{ ucfirst($facility->status) }}</span></p>
                
                @if ($facility->status !== 'available')
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                        <p class="font-bold">Unavailable</p>
                        <p>This facility cannot be booked at the moment.</p>
                    </div>
                @endif
                
                <p class="text-sm text-gray-500 mt-4">
                    Booking is strictly for **today ({{ \Carbon\Carbon::parse($today)->format('F d, Y') }})** only, between 8:00 AM and 6:00 PM.
                </p>
            </div>

            <h3 class="text-xl font-semibold mb-4 text-gray-700">Available Time Slots (Today)</h3>
            
            <!-- Clarification for the user -->
            <p class="text-sm text-yellow-600 bg-yellow-100 p-3 rounded-lg mb-6 border-l-4 border-yellow-500">
                ⚠️ **Note on Slot Status:** Slots are marked as 'Passed' if the current server time is after the slot's start time (e.g., if it's 10:30 AM, the 9:00 AM slot is 'Passed'). 
                Slots are marked 'Full' if the number of overlapping, confirmed bookings reaches the facility's capacity ({{ $facility->capacity }}).
            </p>

            <!-- Time Slot Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="time-slot-grid">
                @php
                    // Define the fixed opening and closing times
                    $opening = \Carbon\Carbon::createFromTimeString('08:00');
                    $closing = \Carbon\Carbon::createFromTimeString('18:00');
                    // Interval is 60 minutes (1 hour)
                    $interval = 60; 
                    $currentTime = \Carbon\Carbon::now();
                @endphp

                @while ($opening->lessThan($closing))
                    @php
                        $slotStart = $opening->copy();
                        $slotEnd = $opening->copy()->addMinutes($interval);
                        
                        // Stop if the next slot exceeds the closing time (18:00)
                        if ($slotEnd->greaterThan($closing)) {
                            break;
                        }

                        $timeDisplay = $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A');
                        
                        // 1. Check if the slot is in the past
                        $isPast = $slotStart->lessThan($currentTime);

                        // 2. Check Capacity (only if the facility is available and the slot is not passed)
                        $isFullyBooked = false;
                        if ($facility->status === 'available' && !$isPast) {
                            $overlappingBookings = \App\Models\Booking::where('facility_id', $facility->id)
                                ->where('booking_date', $today)
                                ->where('status', 'confirmed')
                                ->where(function ($query) use ($slotStart, $slotEnd) {
                                    // Overlap condition: existing_start < new_end AND existing_end > new_start
                                    $query->where('start_time', '<', $slotEnd->format('H:i:s'))
                                        ->where('end_time', '>', $slotStart->format('H:i:s'));
                                })
                                ->count();
                            
                            if ($overlappingBookings >= $facility->capacity) {
                                $isFullyBooked = true;
                            }
                        }
                        
                        // 3. Determine Final Status and Class
                        $buttonClass = 'bg-gray-200 text-gray-500 cursor-not-allowed';
                        $statusText = 'Unavailable';

                        if ($facility->status === 'available') {
                            if ($isPast) {
                                $buttonClass = 'bg-gray-100 text-gray-400 cursor-not-allowed';
                                $statusText = 'Passed';
                            } elseif ($isFullyBooked) {
                                $buttonClass = 'bg-red-500 text-white cursor-not-allowed hover:bg-red-600';
                                $statusText = 'Full';
                            } else {
                                $buttonClass = 'bg-green-500 text-white cursor-pointer hover:bg-green-600 transition duration-150 ease-in-out';
                                $statusText = 'Available';
                            }
                        }

                    @endphp

                    <button 
                        @if ($statusText === 'Available') 
                            {{-- FIX: Using data-attributes to store values and a JS listener to attach behavior, avoiding inline JS quoting issues. --}}
                            data-start-time="{{ $slotStart->format('H:i:s') }}"
                            data-end-time="{{ $slotEnd->format('H:i:s') }}"
                            data-time-display="{{ $timeDisplay }}"
                            class="book-slot-btn {{ $buttonClass }}"
                        @else
                            disabled
                            class="{{ $buttonClass }}"
                        @endif
                        class="p-4 rounded-lg shadow-md font-medium text-sm text-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 h-20 flex flex-col justify-center items-center"
                    >
                        <span class="text-base">{{ $slotStart->format('g:i A') }}</span>
                        <span class="text-xs mt-1">{{ $statusText }}</span>
                    </button>
                    
                    @php
                        // Move to the next interval (60 minutes)
                        $opening->addMinutes($interval);
                    @endphp
                @endwhile
            </div>

            <!-- Error/Success Messages -->
            @if (session('success'))
                <div class="mt-8 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mt-8 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Booking Modal Structure -->
    <div id="booking-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Confirm Your Booking
            </h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">
                    You are booking **{{ $facility->name }}** for the slot: <span id="slot-display" class="font-semibold text-indigo-600"></span>.
                </p>

                <form id="booking-form" method="POST" action="{{ route('student.bookings.store') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="facility_id" value="{{ $facility->id }}">
                    <input type="hidden" name="booking_date" value="{{ $today }}">
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
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition duration-150">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for Modal Handling and Event Listeners -->
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
</x-app-layout>