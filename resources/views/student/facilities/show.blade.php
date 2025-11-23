@extends('layouts.student')

@section('title', 'Book ' . $facility->name)

@section('content')
<div class="max-w-4xl mx-auto p-6 space-y-8">
    <div class="flex justify-between items-center pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800">{{ $facility->name }}</h1>
        <a href="{{ route('student.facilities.index') }}" class="text-indigo-600 hover:text-indigo-800 transition duration-150 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Facilities
        </a>
    </div>

    <!-- Facility Description and Details -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <p class="text-gray-600 mb-4">{{ $facility->description }}</p>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="font-semibold text-gray-700">Type:</span> {{ ucfirst($facility->type) }}</div>
            <div><span class="font-semibold text-gray-700">Capacity:</span> {{ $facility->capacity }} people</div>
            <div><span class="font-semibold text-gray-700">Fixed Hours:</span> 8:00 AM - 6:00 PM</div>
            <div><span class="font-semibold text-gray-700">Duration:</span> 1 Hour (Fixed)</div>
        </div>
    </div>

    <!-- Booking Interface -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-indigo-100">
        <h2 class="text-2xl font-semibold mb-4 text-indigo-700">Book Today: {{ \Carbon\Carbon::parse($bookingDate)->format('l, F jS') }}</h2>
        <p class="text-sm text-gray-500 mb-6">Select an available 1-hour slot below. All bookings are strictly for today.</p>

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('student.bookings.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Hidden Fields (Date and Facility) -->
            <input type="hidden" name="facility_id" value="{{ $facility->id }}">
            <input type="hidden" name="booking_date" value="{{ $bookingDate }}">

            <!-- 1. Start Time Selection -->
            <div>
                <label for="start_time" class="block text-sm font-medium text-gray-700">1. Select Start Time (1-Hour Slot)</label>
                <select id="start_time" name="start_time" required 
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                    <option value="">-- Select a Time Slot --</option>
                    
                    @forelse ($availableSlots as $slot)
                        @if ($slot['is_available'])
                            <!-- Data attributes store the label and the calculated end time for JS/form submission -->
                            <option value="{{ $slot['start_time'] }}" 
                                    data-end-time="{{ $slot['end_time'] }}"
                                    data-label="{{ $slot['label'] }}">
                                {{ $slot['label'] }} ({{ $facility->capacity - $slot['current_bookings'] }} spots left)
                            </option>
                        @else
                            <!-- Display unavailable slots, but make them unselectable/disabled -->
                            <option value="{{ $slot['start_time'] }}" disabled class="bg-gray-100 text-gray-400">
                                {{ $slot['label'] }} (Fully Booked or Past)
                            </option>
                        @endif
                    @empty
                        <option value="" disabled>No available slots remaining today.</option>
                    @endforelse
                    
                </select>
                @error('start_time')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- HIDDEN END TIME FIELD (Set by JS) -->
            <!-- This is crucial: the end time must be passed to the server for validation and storage -->
            <input type="hidden" name="end_time" id="end_time" required>

            <!-- Display confirmation text -->
            <p id="confirmation_text" class="text-sm text-indigo-600 hidden p-3 border border-indigo-200 bg-indigo-50 rounded-md">
                You will be booking the slot: <span class="font-bold" id="selected_slot_label"></span>.
            </p>

            <!-- 2. Notes (Optional) -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">2. Notes (Optional)</label>
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submit_button" disabled class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-400 focus:outline-none transition duration-150">
                Confirm Booking
            </button>
        </form>
    </div>
</div>

<!-- JavaScript for Setting End Time and Enabling Button -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startTimeSelect = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const submitButton = document.getElementById('submit_button');
        const confirmationText = document.getElementById('confirmation_text');
        const selectedSlotLabel = document.getElementById('selected_slot_label');

        const updateBookingDetails = () => {
            const selectedOption = startTimeSelect.options[startTimeSelect.selectedIndex];
            
            // Check if a valid, non-placeholder option is selected
            if (selectedOption && selectedOption.value) {
                const startTime = selectedOption.value;
                const endTime = selectedOption.dataset.endTime;
                const label = selectedOption.dataset.label;

                // Set the hidden end time input for the server
                endTimeInput.value = endTime;
                
                // Update confirmation message
                selectedSlotLabel.textContent = label;
                confirmationText.classList.remove('hidden');

                // Enable the submit button and change style
                submitButton.disabled = false;
                submitButton.classList.remove('bg-gray-400');
                submitButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'focus:ring-indigo-500');

            } else {
                // Reset fields and disable button
                endTimeInput.value = '';
                confirmationText.classList.add('hidden');
                submitButton.disabled = true;
                submitButton.classList.add('bg-gray-400');
                submitButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'focus:ring-indigo-500');
            }
        };

        startTimeSelect.addEventListener('change', updateBookingDetails);
        
        // Initial run to handle page load state
        updateBookingDetails();
    });
</script>
@endsection