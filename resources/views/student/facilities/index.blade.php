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
            'closing_time' => '06:00 PM',
            'color_scheme' => 'bg-blue-100 text-blue-800 border-blue-500',
            'image_url' => asset('assets/Discussion Room 2.jpg'), 
        ],
        'pad' => [
            'icon' => 'fas fa-bed text-indigo-600',
            'title' => 'Nap Pad',
            'general_description' => 'Dedicated quiet zones for students needing short, restorative naps between classes. Bookings are strictly limited to short durations to ensure fair access.',
            'opening_time' => '08:00 AM',
            'closing_time' => '06:00 PM',
            'color_scheme' => 'bg-indigo-100 text-indigo-800 border-indigo-500',
            'image_url' => asset('assets/Nap Pads 2.jpg'), 
        ],
        'venue' => [
            'icon' => 'fas fa-building text-orange-600',
            'title' => 'Activity Center Venue',
            'general_description' => 'A large, multi-purpose venue suitable for major campus events, workshops, guest lectures, and large student gatherings. This is booked as a venue, not for individual use.',
            'opening_time' => '08:00 AM',
            'closing_time' => '06:00 PM',
            'color_scheme' => 'bg-orange-100 text-orange-800 border-orange-500',
            'image_url' => asset('assets/Activity Center 2.jpg'), 
        ],
        default => [
            'icon' => 'fas fa-tools text-gray-600',
            'title' => 'All Available Facilities',
            'general_description' => 'Browse all available discussion rooms, nap pads, and venues below. Click "Book Now" to view time slots.',
            'opening_time' => '08:00 AM',
            'closing_time' => '06:00 PM',
            'color_scheme' => 'bg-gray-100 text-gray-800 border-gray-500',
            'image_url' => asset('assets/all_facilities.jpg'), 
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

{{-- 2. HEADER AND OVERVIEW SECTION (INCLUDING NESTED FACILITY CARDS) --}}
<div class="mb-8">
    {{-- Back Button --}}
    <a href="{{ route('student.home') }}" class="text-green-600 hover:text-green-700 text-sm mb-3 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Home
    </a>

    {{-- Dynamic Overview Card - Contains ALL content now --}}
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg border-l-8 {{ $typeConfig['color_scheme'] }}">
        
        {{-- 1. TOP SECTION: Image, Text, and Metrics (All in a row) --}}
        {{-- Use items-stretch to make columns the same height --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 items-stretch">
            
            {{-- Image Column (1/3 width - LEFT) --}}
            <div class="md:col-span-1 hidden md:block">
                {{-- Container is flex column to push the caption down --}}
                <div class="flex flex-col h-full"> 
                    <img 
                        src="{{ $typeConfig['image_url'] }}" 
                        alt="Image of a {{ $typeConfig['title'] }}" 
                        {{-- ENHANCEMENT: Use h-full and object-cover to match the height of the adjacent column --}}
                        class="w-full h-full object-cover rounded-lg shadow-md"
                    >
                </div>
            </div>

            {{-- Text/Metrics Column (2/3 width - RIGHT) --}}
            <div class="md:col-span-2">
                <div class="flex items-center mb-3">
                    <i class="{{ $typeConfig['icon'] }} text-3xl mr-4"></i>
                    <h1 class="text-3xl font-extrabold text-gray-800">{{ $typeConfig['title'] }} Overview</h1>
                </div>
                <p class="text-gray-600 mb-4">{{ $typeConfig['general_description'] }}</p>

                {{-- Overview Metrics --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-center border-t pt-4 mt-4">
                    <div>
                        <p class="font-bold text-gray-700">Operating Hours</p>
                        <p class="text-green-600">{{ $typeConfig['opening_time'] }} - {{ $typeConfig['closing_time'] }}</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-700">Max Duration</p>
                        <p class="text-green-600">1 Hour (Fixed)</p>
                    </div>
                    <div class="hidden lg:block">
                        <p class="font-bold text-gray-700">Booking Capacity</p>
                        <p class="text-green-600">Per Room / Pad</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-700">Available Today</p>
                        <p class="text-green-600">
                            {{ $filteredFacilities->count() }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
        
        {{-- 2. SEPARATOR --}}
        <hr class="mb-6">

        {{-- 3. BOTTOM SECTION: INDIVIDUAL FACILITY CAROUSEL (Starts below the entire Overview) --}}
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            Available {{ $filterType === 'all' ? 'Facilities' : $typeConfig['title'] . 's' }}
        </h2>

        @if ($filteredFacilities->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border border-dashed border-yellow-500">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                <p class="text-sm font-semibold text-gray-600 text-center">
                    No {{ $typeConfig['title'] }}s Currently Listed.
                </p>
            </div>
        @else
            {{-- Facility Cards Carousel Container (Horizontal Scrolling) --}}
            <div class="flex space-x-4 pb-4 overflow-x-auto custom-scrollbar">
                @foreach ($filteredFacilities as $facility)
                    @php
                        $cardColorScheme = $cardConfigMap[$facility->type] ?? $cardConfigMap['default'];
                        $cardColorClasses = $cardColorScheme['color_scheme'];
                        $iconBgColor = str_replace(['-100', 'text-'], ['-600', 'text-'], explode(' ', $cardColorClasses)[0]);
                    @endphp

                    {{-- Card width is fixed to w-72 (18rem) to force horizontal scrolling --}}
                    <div class="bg-gray-50 shadow-md rounded-lg flex-shrink-0 w-72 overflow-hidden hover:shadow-lg transition duration-300 border border-gray-200">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center space-x-2">
                                    {{-- Icon Circle --}}
                                    <div class="w-8 h-8 flex items-center justify-center rounded-full {{ $iconBgColor }} text-white text-md">
                                        <i class="{{ $cardColorScheme['icon'] }}"></i>
                                    </div>
                                    {{-- Capacity --}}
                                    <div class="text-xs font-semibold text-gray-600 flex items-center">
                                        <i class="fas fa-users mr-1 text-xs text-gray-400"></i>
                                        Cap: {{ $facility->capacity }}
                                    </div>
                                </div>

                                {{-- Status Tag --}}
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Available
                                </span>
                            </div>

                            <h4 class="text-md font-bold text-gray-900 mb-1 truncate">{{ $facility->name }}</h4>

                            <p class="text-gray-600 mb-3 h-8 overflow-hidden text-xs">
                                {{ Str::limit($facility->description, 50, '...') ?: 'Facility description.' }}
                            </p>

                            {{-- Call to Action --}}
                            <a href="{{ route('student.facilities.show', $facility->id) }}" 
                                class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                                <i class="fas fa-calendar-check mr-2"></i> View Slots
                            </a>
                        </div>
                    </div>
                @endforeach 
            </div>
            
            {{-- Optional: Scroll Hint --}}
            @if ($filteredFacilities->count() > 3)
                <p class="text-center text-sm text-gray-500 mt-2">
                    <i class="fas fa-angle-double-right text-green-500 mr-1"></i> Scroll horizontally to view all {{ $typeConfig['title'] }}s
                </p>
            @endif
        @endif
    </div>
</div>

{{-- ------------------------------------------------------------- --}}

@endsection