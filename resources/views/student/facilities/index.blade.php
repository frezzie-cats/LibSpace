@extends('layouts.student')
@section('title', 'Facilities: ' . (request('type') ? ucfirst(request('type')) : 'All'))
@section('content')

@php
// --- 1. Configuration based on Facility Type ---
$filterType = request('type', 'all');

// Define general purpose configuration for the *header* based on the filter type
$typeConfig = match ($filterType) {
    'room' => [
        'icon' => 'fas fa-door-open text-blue-600',
        'title' => 'Discussion Room',
        'general_description' => 'These rooms are designed for small group collaborations, presentations, and quiet study sessions. They are equipped with whiteboards and power outlets.',
        'opening_time' => '08:00 AM',
        'closing_time' => '18:00 PM',
        'color_scheme' => 'bg-blue-100 text-blue-800 border-blue-500',
    ],
    'pad' => [
        'icon' => 'fas fa-bed text-indigo-600',
        'title' => 'Nap Pad',
        'general_description' => 'Dedicated quiet zones for students needing short, restorative naps between classes. Bookings are strictly limited to short durations to ensure fair access.',
        'opening_time' => '08:00 AM',
        'closing_time' => '18:00 PM',
        'color_scheme' => 'bg-indigo-100 text-indigo-800 border-indigo-500',
    ],
    'venue' => [
        'icon' => 'fas fa-building text-orange-600',
        'title' => 'Activity Center Venue',
        'general_description' => 'A large, multi-purpose venue suitable for major campus events, workshops, guest lectures, and large student gatherings. This is booked as a venue, not for individual use.',
        'opening_time' => '08:00 AM',
        'closing_time' => '18:00 PM',
        'color_scheme' => 'bg-orange-100 text-orange-800 border-orange-500',
    ],
    default => [
        'icon' => 'fas fa-tools text-gray-600',
        'title' => 'All Available Facilities',
        'general_description' => 'Browse all available discussion rooms, nap pads, and venues below. Click "Book Now" to view time slots.',
        'opening_time' => 'N/A',
        'closing_time' => 'N/A',
        'color_scheme' => 'bg-gray-100 text-gray-800 border-gray-500',
    ],
};

// --- Configuration Map for Individual Facility Cards (Fixes the Undefined array key "default" Error) ---
$cardConfigMap = [
    'room' => [
        'icon' => 'fas fa-door-open text-blue-600',
        'color_scheme' => 'bg-blue-100 text-blue-800 border-blue-500',
    ],
    'pad' => [
        'icon' => 'fas fa-bed text-indigo-600',
        'color_scheme' => 'bg-indigo-100 text-indigo-800 border-indigo-500',
    ],
    'venue' => [
        'icon' => 'fas fa-building text-orange-600',
        'color_scheme' => 'bg-orange-100 text-orange-800 border-orange-500',
    ],
    'default' => [ 
        'icon' => 'fas fa-tools text-gray-600',
        'color_scheme' => 'bg-gray-100 text-gray-800 border-gray-500',
    ],
];

// 2. Data Filtering and Setup
$facilities = $facilities ?? collect([]); // Ensure $facilities exists
$filteredFacilities = ($filterType === 'all')
? $facilities
: $facilities->filter(fn ($facility) => $facility->type === $filterType);

// Check if a specific facility is being requested for booking (inline form)
$bookingFacilityId = request('facility_id');
$bookingFacility = $bookingFacilityId ? $facilities->firstWhere('id', $bookingFacilityId) : null;

// --- SIMULATED DATA (Replace this with actual Controller logic) ---
$bookingDate = now()->format('Y-m-d');
$availableSlots = collect([
    // Assuming capacity is 5 for all facilities for simulation
    ['start_time' => '09:00', 'end_time' => '10:00', 'label' => '9:00 AM - 10:00 AM', 'is_available' => true, 'current_bookings' => 0],
    ['start_time' => '10:00', 'end_time' => '11:00', 'label' => '10:00 AM - 11:00 AM', 'is_available' => true, 'current_bookings' => 1],
    ['start_time' => '11:00', 'end_time' => '12:00', 'label' => '11:00 AM - 12:00 PM', 'is_available' => false, 'current_bookings' => 5],
]);
// --- END SIMULATED DATA ---
@endphp

<div class="mb-8">
    <a href="{{ route('student.home') }}" class="text-green-600 hover:text-green-700 text-sm mb-3 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Home
    </a>

    <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg border-l-8 {{ $typeConfig['color_scheme'] }}">
        <div class="flex items-center mb-3">
            <i class="{{ $typeConfig['icon'] }} text-3xl mr-4"></i>
            <h1 class="text-3xl font-extrabold text-gray-800">{{ $typeConfig['title'] }} Overview</h1>
        </div>
        <p class="text-gray-600 mb-4">{{ $typeConfig['general_description'] }}</p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-center border-t pt-4 mt-4">
            <div>
                <p class="font-bold text-gray-700">Operating Hours</p>
                <p class="text-green-600">{{ $typeConfig['opening_time'] }} - {{ $typeConfig['closing_time'] }}</p>
            </div>
            <div>
                <p class="font-bold text-gray-700">Max Duration</p>
                <p class="text-green-600">1 Hour (Fixed)</p>
            </div>
            <div class="hidden md:block">
                <p class="font-bold text-gray-700">Booking Capacity</p>
                <p class="text-green-600">Per Room / Pad</p>
            </div>
            <div class="hidden md:block">
                <p class="font-bold text-gray-700">Available Today</p>
                <p class="text-green-600">
                    {{ $filteredFacilities->count() }} {{ $filterType === 'all' ? 'Facilities' : $filterType }}
                </p>
            </div>
        </div>
    </div>
</div>

<hr>

<h2 class="text-2xl font-bold text-gray-800 mb-4 mt-8">
    {{ $filterType === 'all' ? 'Browse Individual Facilities' : 'Select a specific ' . $typeConfig['title'] }}
</h2>

@if ($filteredFacilities->isEmpty())
<div class="flex flex-col items-center justify-center p-10 bg-white rounded-lg shadow-xl border-t-4 border-yellow-500">
    <i class="fas fa-exclamation-triangle text-yellow-500 text-5xl mb-4"></i>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">
        No {{ $filterType === 'all' ? 'Facilities' : $typeConfig['title'] }} Available
    </h3>
    <p class="text-gray-500">Please check back later or remove the filters.</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($filteredFacilities as $facility)
        @php
            $cardColorScheme = $cardConfigMap[$facility->type] ?? $cardConfigMap['default'];
            $cardColorClasses = $cardColorScheme['color_scheme'];
            $iconBgColor = str_replace(['bg-100', 'text-'], ['bg-600', 'text-'], explode(' ', $cardColorClasses)[0]);
        @endphp
        
        <div class="bg-white shadow-lg rounded-xl overflow-hidden transform hover:shadow-2xl transition duration-300">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-3">
                        {{-- Icon Circle --}}
                        <div class="w-10 h-10 flex items-center justify-center rounded-full {{ $iconBgColor }} text-white text-lg">
                            <i class="{{ $cardColorScheme['icon'] }}"></i>
                        </div>
                        {{-- Capacity --}}
                        <div class="text-sm font-semibold text-gray-600 flex items-center">
                            <i class="fas fa-users mr-1 text-sm text-gray-400"></i>
                            Cap: {{ $facility->capacity }}
                        </div>
                    </div>

                    {{-- Status Tag (Simplified, assuming all listed are available) --}}
                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                        Available
                    </span>
                </div>

                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $facility->name }}</h2>

                <p class="text-gray-600 mb-4 h-12 overflow-hidden text-sm">
                    {{ Str::limit($facility->description, 80, '...') ?: 'General facility description.' }}
                </p>

                {{-- Link now points back to the index page with the facility_id to show the form at the bottom --}}
                <a href="{{ route('student.facilities.index', ['type' => $filterType, 'facility_id' => $facility->id]) }}#booking-form-anchor" 
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                    <i class="fas fa-calendar-check mr-2"></i> Book Now
                </a>
            </div>
        </div>
    @endforeach 
</div>
@endif

<hr>

@if ($bookingFacility)
<div class="bg-white shadow-2xl rounded-2xl p-6 lg:p-8 mt-10 border-t-4 border-green-500" id="booking-form-anchor">
    <h2 class="text-2xl font-bold text-green-700 mb-5 flex items-center">
        <i class="fas fa-calendar-alt mr-3"></i> Book: {{ $bookingFacility->name }}
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 space-y-4">
            {{-- Room Details --}}
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-2">Room Specifics</h3>
                <p class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <i class="fas fa-users mr-1 text-sm text-gray-400"></i>
                    Capacity: <span id="current_capacity">{{ $bookingFacility->capacity }}</span> people
                </p>
                <p id="current_description" class="text-gray-700 leading-relaxed text-sm">
                    {{ $bookingFacility->description ?: 'No specific description provided for this room.' }}
                </p>
            </div>
            
            {{-- Date Selector (for future enhancement) --}}
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
                <p class="font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-yellow-500"></i>
                    Booking for: 
                    <span class="font-bold ml-1">{{ \Carbon\Carbon::parse($bookingDate)->format('l, F jS') }}</span>
                </p>
                <p class="text-gray-600 mt-1">Date selection not implemented. All bookings are for today.</p>
            </div>
            
        </div>
        
        <div class="md:col-span-2">
            <form action="{{ route('student.bookings.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <input type="hidden" name="facility_id" value="{{ $bookingFacility->id }}">
                <input type="hidden" name="booking_date" value="{{ $bookingDate }}">
                
                <div>
                    <label for="start_time" class="block text-sm font-bold text-gray-700">1. Select Start Time (1-Hour Slot)</label>
                    <select id="start_time" name="start_time" required 
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md shadow-sm">
                        <option value="">-- Select a Time Slot --</option>
                        
                        @forelse ($availableSlots as $slot)
                            @if ($slot['is_available'])
                                <option value="{{ $slot['start_time'] }}" data-end-time="{{ $slot['end_time'] }}" data-label="{{ $slot['label'] }}">
                                    {{ $slot['label'] }} ({{ $bookingFacility->capacity - $slot['current_bookings'] }} spots left)
                                </option>
                            @else
                                <option value="{{ $slot['start_time'] }}" disabled class="bg-gray-100 text-gray-400">
                                    {{ $slot['label'] }} (Fully Booked)
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
                
                <input type="hidden" name="end_time" id="end_time" required>

                <p id="confirmation_text" class="text-sm text-green-600 hidden p-3 border border-green-200 bg-green-50 rounded-md">
                    Booking <span class="font-bold">{{ $bookingFacility->name }}</span> for <span class="font-bold" id="selected_slot_label"></span>.
                </p>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">2. Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>

                <button type="submit" id="submit_button" disabled class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-400 focus:outline-none transition duration-150">
                    Confirm Booking
                </button>
                <a href="{{ route('student.facilities.index', ['type' => $filterType]) }}" class="w-full text-center block mt-2 text-gray-500 hover:text-gray-700 text-sm">Cancel and choose a different {{ $filterType }}</a>
            </form>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startTimeSelect = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const submitButton = document.getElementById('submit_button');
    const confirmationText = document.getElementById('confirmation_text');
    const selectedSlotLabel = document.getElementById('selected_slot_label');

    // Scroll to the booking form if a facility is selected
    if (document.getElementById('booking-form-anchor')) {
        document.getElementById('booking-form-anchor').scrollIntoView({ behavior: 'smooth' });
    }

    // Function to update the booking form based on slot selection
    const updateBookingDetails = () => {
        if (!startTimeSelect) return; // Guard for when the form is not visible

        const selectedOption = startTimeSelect.options[startTimeSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const endTime = selectedOption.dataset.endTime;
            const label = selectedOption.dataset.label;

            endTimeInput.value = endTime;
            selectedSlotLabel.textContent = label;
            confirmationText.classList.remove('hidden');

            submitButton.disabled = false;
            submitButton.classList.remove('bg-gray-400');
            submitButton.classList.add('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500');

        } else {
            endTimeInput.value = '';
            selectedSlotLabel.textContent = '';
            confirmationText.classList.add('hidden');
            
            submitButton.disabled = true;
            submitButton.classList.add('bg-gray-400');
            submitButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500');
        }
    };

    // Event Listener
    if (startTimeSelect) {
        startTimeSelect.addEventListener('change', updateBookingDetails);
    }

    // Initial run to set up booking state if a slot is pre-selected (not likely here, but good practice)
    updateBookingDetails();
});
</script>
@endsection