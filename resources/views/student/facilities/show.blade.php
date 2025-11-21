@extends('layouts.student')

@section('content')
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-800">Book: {{ $facility->name }}</h1>
        <a href="{{ route('student.facilities.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition duration-150">
            &larr; Back to Facilities
        </a>
    </div>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Alpine.js component to manage date and slot selection -->
    <div x-data="{ 
        // Initial values passed from the Laravel controller
        bookingDate: '{{ $bookingDate }}', 
        
        // Holds the currently selected time slot object: {start_time, end_time, label}
        selectedSlot: null,
        
        // Facility ID for form submission
        facilityId: '{{ $facility->id }}',
        
        // Function to select a slot
        selectSlot(start, end, label) {
            // If the user clicks the currently selected slot, deselect it (toggle)
            if (this.selectedSlot && this.selectedSlot.start_time === start) {
                this.selectedSlot = null;
            } else {
                this.selectedSlot = { start_time: start, end_time: end, label: label };
            }
        },

        // Function to handle date change and refresh the page with new data
        handleDateChange(event) {
            const newDate = event.target.value;
            // Clear slot selection when date changes
            this.selectedSlot = null;
            // Redirect the user to the same page with the new date query parameter
            window.location.href = '{{ route('student.facilities.show', $facility) }}?date=' + newDate;
        }

    }" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Facility Information Panel -->
        <div class="lg:col-span-1 bg-white shadow-xl rounded-xl p-6 h-full">
            <div class="flex items-center space-x-4 mb-4 pb-4 border-b">
                @php
                    $icon = match ($facility->type) {
                        'room' => 'fas fa-door-open text-blue-600',
                        'pad' => 'fas fa-bed text-indigo-600',
                        'equipment' => 'fas fa-laptop-code text-green-600',
                        default => 'fas fa-tools text-gray-600',
                    };
                @endphp
                <i class="{{ $icon }} text-3xl"></i>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">{{ $facility->name }}</h3>
                    <p class="text-sm text-gray-500 uppercase">{{ ucfirst($facility->type) }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center text-gray-700">
                    <i class="fas fa-users w-5 mr-3 text-sm text-gray-400"></i>
                    <span class="font-medium">Capacity:</span> {{ $facility->capacity }} people
                </div>
                <div class="flex items-center text-gray-700">
                    <i class="fas fa-check-circle w-5 mr-3 text-sm text-green-500"></i>
                    <span class="font-medium">Status:</span> 
                    <span class="ml-1 text-green-600 font-semibold">{{ ucfirst($facility->status) }}</span>
                </div>
                <div class="pt-3 border-t mt-3">
                    <h4 class="font-semibold text-gray-700 mb-1">Description:</h4>
                    <p class="text-sm text-gray-600">{{ $facility->description ?: 'No detailed description available.' }}</p>
                </div>
                
                <!-- Booking Summary -->
                <div x-show="selectedSlot" x-cloak class="p-3 mt-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <h4 class="font-bold text-indigo-800 mb-1">Your Selection:</h4>
                    <p class="text-sm text-indigo-700">Date: <span x-text="bookingDate"></span></p>
                    <p class="text-sm text-indigo-700">Time: <span x-text="selectedSlot.label"></span></p>
                </div>
            </div>
        </div>

        <!-- Booking Form / Time Slot Selection -->
        <div class="lg:col-span-2 bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Select Date & Time Slot</h2>
            
            <form id="booking-form" action="{{ route('student.bookings.store') }}" method="POST">
                @csrf
                <!-- Hidden fields updated by Alpine.js before submission -->
                <input type="hidden" name="facility_id" :value="facilityId">
                <input type="hidden" name="booking_date" :value="bookingDate">
                <!-- Use optional chaining (?) here to avoid errors if selectedSlot is null -->
                <input type="hidden" name="start_time" :value="selectedSlot?.start_time">
                <input type="hidden" name="end_time" :value="selectedSlot?.end_time">
                
                <!-- Date Picker -->
                <div class="mb-4">
                    <label for="date-picker" class="block text-sm font-bold text-gray-700 mb-1">Booking Date</label>
                    <input 
                        type="date" 
                        id="date-picker" 
                        name="date-picker-input" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        :value="bookingDate"
                        min="{{ date('Y-m-d') }}"
                        @change="handleDateChange"
                        required>
                    @error('booking_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Time Slot Selection Grid -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Available Time Slots for {{ $selectedDate->format('D, M j, Y') }}</label>
                    
                    @if (empty($availableSlots))
                        <div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg text-sm">
                            No available slots found for this date, or the booking window is closed for today.
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($availableSlots as $slot)
                                @php
                                    // Set color based on remaining capacity
                                    $remainingCapacity = $facility->capacity - $slot['current_bookings'];
                                    $colorClass = 'bg-white text-gray-800 hover:bg-gray-100 border-gray-300';
                                    if ($remainingCapacity <= 1) {
                                        $colorClass = 'bg-red-50 text-red-800 hover:bg-red-100 border-red-300'; // Low capacity warning
                                    } elseif ($slot['current_bookings'] > 0) {
                                        $colorClass = 'bg-yellow-50 text-yellow-800 hover:bg-yellow-100 border-yellow-300'; // Some capacity taken
                                    }
                                @endphp
                                
                                <button 
                                    type="button" 
                                    @click="selectSlot('{{ $slot['start_time'] }}', '{{ $slot['end_time'] }}', '{{ $slot['label'] }}')"
                                    :class="{
                                        'ring-2 ring-indigo-500 bg-indigo-50 text-indigo-700 border-indigo-500': selectedSlot?.start_time === '{{ $slot['start_time'] }}',
                                        'opacity-50 cursor-not-allowed': !{{ $slot['is_future'] ? 'true' : 'false' }},
                                        '{{ $colorClass }}': selectedSlot?.start_time !== '{{ $slot['start_time'] }}'
                                    }"
                                    class="p-3 border rounded-lg shadow-sm text-sm font-medium transition duration-150 relative text-left"
                                    title="Bookings: {{ $slot['current_bookings'] }} / {{ $facility->capacity }}"
                                    {{ $slot['is_future'] ? '' : 'disabled' }}
                                >
                                    <span class="block font-semibold">{{ $slot['label'] }}</span>
                                    <span class="text-xs font-mono opacity-80 mt-0.5 block">
                                        {{ $remainingCapacity }} slot{{ $remainingCapacity !== 1 ? 's' : '' }} remaining
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <button 
                    type="submit" 
                    :disabled="!selectedSlot"
                    class="w-full px-6 py-3 text-white font-semibold rounded-lg shadow-lg transition duration-150 disabled:bg-gray-400 disabled:cursor-not-allowed"
                    :class="selectedSlot ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400'">
                    Confirm Booking
                </button>
            </form>
        </div>
    </div>
@endsection