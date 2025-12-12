@extends('layouts.staff')

{{-- PAGE HEADER (SECTION) --}}
@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Library Management Reports') }}
</h2>
@endsection

{{-- MAIN CONTENT AREA (SECTION) --}}
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            {{-- REPORT FILTER --}}
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                {{-- Dashboard Title and Period --}}
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Operational Dashboard</h1>
                    <p class="text-gray-600 mt-2">Data aggregated for the period: <span class="font-semibold text-indigo-600">{{ $reportPeriod }}</span></p>
                </div>

                {{-- Filter Form (Flex container ensures single row alignment) --}}
                <form id="reportFilterForm" method="GET" action="{{ route('staff.reports.index') }}" class="flex items-center space-x-3">
                    <label for="period" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter By:</label>
                    <select 
                        id="period" 
                        name="period" 
                        {{-- Removed mt-1 class to ensure perfect vertical alignment --}}
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm"
                    >
                        {{-- Determine the current period selected, defaulting to 'monthly' --}}
                        @php $currentPeriod = request('period', 'monthly'); @endphp
                        
                        <option value="weekly" @if($currentPeriod == 'weekly') selected @endif>Last 7 Days (Weekly)</option>
                        <option value="monthly" @if($currentPeriod == 'monthly') selected @endif>Last 30 Days (Monthly)</option>
                        <option value="quarterly" @if($currentPeriod == 'quarterly') selected @endif>Last 90 Days (Quarterly)</option>
                        <option value="all" @if($currentPeriod == 'all') selected @endif>All Time</option>
                    </select>
                </form>
            </div>


            {{-- STATS CARDS GRID (Row 1) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                
                {{-- CARD 1: OVERALL FEEDBACK SUMMARY (Span 1) --}}
                <div class="bg-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-indigo-800 mb-4">Overall Feedback Summary</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Feedback Items</p>
                            <p class="text-2xl font-extrabold text-indigo-900">{{ number_format($feedbackAnalysis['total_feedback']) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Average Rating (out of 5)</p>
                            <p class="text-3xl font-extrabold text-green-600">{{ $feedbackAnalysis['average_rating'] }} / 5</p>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-t border-indigo-200">
                        <h4 class="text-sm font-medium text-indigo-700 mb-2">Status Breakdown:</h4>
                        <ul class="text-sm space-y-1">
                            @foreach($feedbackAnalysis['status_counts'] as $status => $count)
                                <li class="flex justify-between">
                                    <span class="capitalize">{{ $status }}</span>
                                    <span class="font-bold">{{ $count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- CARD 2: TOP 5 FACILITY USAGE (Span 2) --}}
                <div class="bg-sky-50 border-l-4 border-sky-500 rounded-lg shadow-md p-6 col-span-1 lg:col-span-2">
                    <h3 class="text-xl font-semibold text-sky-800 mb-4">Top 5 Most Booked Facilities ({{ $reportPeriod }})</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-sky-200">
                            <thead class="bg-sky-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-500 uppercase tracking-wider">Facility Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-sky-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-sky-500 uppercase tracking-wider">Total Bookings</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-sky-100">
                                @forelse($usageReport as $reportItem)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $reportItem->facility->name ?? 'Facility Deleted' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                            {{ $reportItem->facility->type ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-sky-600">
                                            {{ $reportItem->total_bookings }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No bookings recorded for this period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> {{-- END STATS CARDS GRID --}}

            {{-- FACILITY RATING PERFORMANCE TABLE (Row 2) --}}
            <div class="mt-8 bg-white border-t border-gray-200 pt-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Facility & Area Performance (By User Rating)</h2>
                <p class="text-gray-600 mb-6">Average rating based on feedback where a rating was provided.</p>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area/Subject</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Average Rating</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ratings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance Meter</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($facilityRatings as $ratingItem)
                                @php
                                    $averageRating = $ratingItem->average_rating;
                                    
                                    // Conditional logic for color and text class
                                    if ($averageRating >= 4.5) {
                                        $textColorClass = 'text-green-600';
                                        $barColorHex = '#10b981'; // Tailwind green-500
                                    } elseif ($averageRating >= 3.0) {
                                        $textColorClass = 'text-yellow-600';
                                        $barColorHex = '#f59e0b'; // Tailwind yellow-500
                                    } else {
                                        $textColorClass = 'text-red-600';
                                        $barColorHex = '#ef4444'; // Tailwind red-500
                                    }
                                    
                                    $progressBarWidth = ($averageRating / 5) * 100;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">
                                        {{ $ratingItem->subject }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-lg font-bold {{ $textColorClass }}">
                                        {{ round($averageRating, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        ({{ $ratingItem->total_feedback }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- Simple progress bar based on rating out of 5 --}}
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div 
                                                class="h-2.5 rounded-full" 
                                                style="width: {{ $progressBarWidth }}%; background-color: {{ $barColorHex }};"
                                            ></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No rated feedback found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> {{-- END FACILITY RATING PERFORMANCE TABLE --}}

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelect = document.getElementById('period');
        const filterForm = document.getElementById('reportFilterForm');

        // Automatically submit the form whenever the selection changes
        filterSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    });
</script>
@endsection