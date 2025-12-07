@extends('layouts.student')
@section('title', 'Facilities: ' . (request('type') ? ucfirst(request('type')) : 'All'))

@section('content')

@php
    // --- 1. CONFIGURATION BLOCK: Define dynamic content based on the URL filter (type) ---
    $filterType = request('type', 'all');

    // Define configuration for the main header based on the filter type
    $typeConfig = match ($filterType) {
        'room' => [
            'icon' => 'fas fa-door-open text-blue-600',
            'title' => 'Discussion Room',
            'general_description' => 'These rooms are designed for small group collaborations, presentations, and quiet study sessions. They are equipped with whiteboards and power outlets.',
            'opening_time' => '08:00 AM',
            'closing_time' => '06:00 PM', // CONSISTENCY UPDATE: Changed from 18:00 PM for better 12-hour display
            'color_scheme' => 'bg-blue-100 text-blue-800 border-blue-500',
        ],
        'pad' => [
            'icon' => 'fas fa-bed text-indigo-600',
            'title' => 'Nap Pad',
            'general_description' => 'Dedicated quiet zones for students needing short, restorative naps between classes. Bookings are strictly limited to short durations to ensure fair access.',
            'opening_time' => '08:00 AM',
            'closing_time' => '06:00 PM', // CONSISTENCY UPDATE: Changed from 18:00 PM for better 12-hour display
            'color_scheme' => 'bg-indigo-100 text-indigo-800 border-indigo-500',
        ],
        'venue' => [
            'icon' => 'fas fa-building text-orange-600',
            'title' => 'Activity Center Venue',
            'general_description' => 'A large, multi-purpose venue suitable for major campus events, workshops, guest lectures, and large student gatherings. This is booked as a venue, not for individual use.',
            'opening_time' => '08:00 AM',
            'closing_time' => '06:00 PM', // CONSISTENCY UPDATE: Changed from 18:00 PM for better 12-hour display
            'color_scheme' => 'bg-orange-100 text-orange-800 border-orange-500',
        ],
        default => [
            'icon' => 'fas fa-tools text-gray-600',
            'title' => 'All Available Facilities',
            'general_description' => 'Browse all available discussion rooms, nap pads, and venues below. Click "Book Now" to view time slots.',
            'opening_time' => '08:00 AM',
            'closing_time' => '06:00 PM', // CONSISTENCY UPDATE: Changed from 18:00 PM for better 12-hour display
            'color_scheme' => 'bg-gray-100 text-gray-800 border-gray-500',
        ],
    };

    // Configuration map for individual facility cards (for icon and color)
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

    // Data Filtering: Filter the $facilities collection based on the filter type
    $facilities = $facilities ?? collect([]); // Ensure $facilities exists and is a collection
    $filteredFacilities = ($filterType === 'all')
        ? $facilities
        : $facilities->filter(fn ($facility) => $facility->type === $filterType);
@endphp

{{-- ------------------------------------------------------------- --}}

{{-- 2. HEADER AND OVERVIEW SECTION --}}
<div class="mb-8">
    {{-- Back Button --}}
    <a href="{{ route('student.home') }}" class="text-green-600 hover:text-green-700 text-sm mb-3 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Home
    </a>

    {{-- Dynamic Overview Card --}}
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg border-l-8 {{ $typeConfig['color_scheme'] }}">
        <div class="flex items-center mb-3">
            <i class="{{ $typeConfig['icon'] }} text-3xl mr-4"></i>
            <h1 class="text-3xl font-extrabold text-gray-800">{{ $typeConfig['title'] }} Overview</h1>
        </div>
        <p class="text-gray-600 mb-4">{{ $typeConfig['general_description'] }}</p>

        {{-- Overview Metrics --}}
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

{{-- ------------------------------------------------------------- --}}

{{-- 3. INDIVIDUAL FACILITY GRID --}}
<hr>
<h2 class="text-2xl font-bold text-gray-800 mb-4 mt-8">
    {{ $filterType === 'all' ? 'Browse Individual Facilities' : 'Select a specific ' . $typeConfig['title'] }}
</h2>

@if ($filteredFacilities->isEmpty())
    {{-- Empty State --}}
    <div class="flex flex-col items-center justify-center p-10 bg-white rounded-lg shadow-xl border-t-4 border-yellow-500">
        <i class="fas fa-exclamation-triangle text-yellow-500 text-5xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">
            No {{ $filterType === 'all' ? 'Facilities' : $typeConfig['title'] }} Available
        </h3>
        <p class="text-gray-500">Please check back later or remove the filters.</p>
    </div>
@else
    {{-- Facility Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($filteredFacilities as $facility)
            @php
                $cardColorScheme = $cardConfigMap[$facility->type] ?? $cardConfigMap['default'];
                $cardColorClasses = $cardColorScheme['color_scheme'];
                // Get base color for the icon background by replacing -100 with -600 (e.g., bg-blue-100 -> bg-blue-600)
                $iconBgColor = str_replace(['-100', 'text-'], ['-600', 'text-'], explode(' ', $cardColorClasses)[0]);
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

                        {{-- Status Tag --}}
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                            Available
                        </span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $facility->name }}</h2>

                    <p class="text-gray-600 mb-4 h-12 overflow-hidden text-sm">
                        {{ Str::limit($facility->description, 80, '...') ?: 'General facility description.' }}
                    </p>

                    {{-- Call to Action: Links to the dedicated booking page for this facility --}}
                    <a href="{{ route('student.facilities.show', $facility->id) }}" 
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                        <i class="fas fa-calendar-check mr-2"></i> Book Now
                    </a>
                </div>
            </div>
        @endforeach 
    </div>
@endif

@endsection