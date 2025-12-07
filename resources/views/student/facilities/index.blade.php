@extends('layouts.student')

@section('content')
<div class="mb-8">
<h1 class="text-3xl font-bold text-gray-800 mb-2">Available Facilities</h1>
<p class="text-gray-600">Browse the available discussion rooms, nap pads, and equipment below. Click "Book Now" to view time slots.</p>
</div>

@if ($facilities->isEmpty())
    <div class="flex flex-col items-center justify-center p-10 bg-white rounded-lg shadow-xl border-t-4 border-yellow-500">
        <i class="fas fa-exclamation-triangle text-yellow-500 text-5xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Facilities Available</h3>
        <p class="text-gray-600 text-center">Sorry, all bookable facilities are currently occupied or under maintenance. Please check back later.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($facilities as $facility)
            @php
                // Define icons based on facility type
                $icon = match ($facility->type) {
                    'room' => 'fas fa-door-open text-blue-500',
                    'pad' => 'fas fa-bed text-indigo-500',
                    'equipment' => 'fas fa-laptop-code text-green-500',
                    default => 'fas fa-tools text-gray-500',
                };
                // Define colors based on facility type
                $typeColor = match ($facility->type) {
                    'room' => 'bg-blue-100 text-blue-800',
                    'pad' => 'bg-indigo-100 text-indigo-800',
                    'equipment' => 'bg-green-100 text-green-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp
            <div class="bg-white shadow-lg rounded-xl overflow-hidden transform hover:shadow-2xl transition duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <!-- Icon and Type Tag -->
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full {{ str_replace(['text-', 'bg-'], ['bg-', ''], $typeColor) }} text-white text-lg">
                                <i class="{{ $icon }}"></i>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $typeColor }} uppercase">
                                {{ ucfirst($facility->type) }}
                            </span>
                        </div>
                        
                        <!-- Capacity -->
                        <div class="text-sm font-semibold text-gray-600 flex items-center">
                            <i class="fas fa-users mr-1 text-sm text-gray-400"></i>
                            Capacity: {{ $facility->capacity }}
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $facility->name }}</h2>
                    
                    <p class="text-gray-600 mb-4 h-12 overflow-hidden text-sm">
                        {{ Str::limit($facility->description, 80, '...') ?: 'No description provided.' }}
                    </p>
                    
                    <!-- Book Now Button -->
                    <a href="{{ route('student.facilities.show', $facility) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                        <i class="fas fa-calendar-check mr-2"></i> Book Now
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif


@endsection