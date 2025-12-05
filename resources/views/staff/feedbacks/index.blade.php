@extends('layouts.staff')

@section('title', 'Student Feedback Review')

@section('content')

@php
    use Carbon\Carbon;
    
    // Define available statuses and their styles
    $statuses = [
        'new' => ['label' => 'New', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-800'],
        'reviewed' => ['label' => 'Reviewed', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
        'resolved' => ['label' => 'Resolved', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
        'ignored' => ['label' => 'Ignored', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
    ];

    // Helper to get facility labels (for display purposes in PHP sections)
    $facilityLabels = [
        'discussion' => 'Discussion Room',
        'center' => 'Activity Center',
        'pad' => 'Nap Pad',
        'other' => 'Other Facility',
    ];

    /**
     * Helper function to render star ratings based on a number (PHP version).
     * This is only used for the overall average rating metric.
     * @param float $rating The rating value (1-5).
     * @return string HTML string with star icons.
     */
    function renderStars(float $rating) {
        $html = '';
        $maxStars = 5;
        $fullStars = floor($rating);
        $halfStar = $rating - $fullStars >= 0.5;
        
        for ($i = 1; $i <= $maxStars; $i++) {
            if ($i <= $fullStars) {
                $html .= '<span class="text-yellow-400 text-lg">★</span>';
            } elseif ($i == $fullStars + 1 && $halfStar) {
                // Using a slightly different color to simulate a half-star look
                $html .= '<span class="text-yellow-300 text-lg">★</span>'; 
            } else {
                $html .= '<span class="text-gray-300 text-lg">★</span>';
            }
        }
        return $html;
    }

    // --- MOCK DATA PREPARATION FOR ALPINE (Ensure data is JSON-ready) ---
    $feedbacks = $feedbacks->map(function ($feedback) {
        // Ensure status exists
        if (!isset($feedback->status)) {
            $feedback->status = (str_contains(strtolower($feedback->message), 'issue') || $feedback->rating < 3) ? 'reviewed' : 'new';
        }
        // Ensure data is formatted correctly for JSON encoding
        $feedback->created_at_formatted = Carbon::parse($feedback->created_at)->format('M j');
        return $feedback;
    });

    // --- CALCULATE DYNAMIC METRICS (Using the full collection) ---
    $totalFeedbacks = $feedbacks->count();
    $averageRating = $totalFeedbacks > 0 ? $feedbacks->avg('rating') : 0;
    
    // Group and calculate average rating per facility
    $facilityAverages = $feedbacks->groupBy('subject')->map(function ($group) {
        return [
            'count' => $group->count(),
            'average' => round($group->avg('rating'), 2),
        ];
    })->sortDesc(); 
    
    // Low Rating Count (Mock: assuming rating < 3 is low)
    $lowRatingCount = $feedbacks->where('rating', '<', 3)->count();
    
    // Prepare filter options
    $facilityOptions = $feedbacks->pluck('subject')->unique()->map(function($item) use ($facilityLabels) {
        return ['value' => $item, 'label' => $facilityLabels[$item] ?? ucfirst($item)];
    })->prepend(['value' => '', 'label' => 'All Facilities']);

@endphp

<div class="space-y-8">
    
    {{-- HEADER --}}
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Student Feedback Dashboard</h1>
        <p class="text-gray-600">Review, analyze, and manage all student feedback submissions regarding facility usage.</p>
    </header>

    {{-- TOP METRICS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Total Submissions --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-indigo-500">
            <p class="text-sm font-medium text-gray-500">Total Submissions</p>
            <p class="mt-1 text-3xl font-semibold text-indigo-900">{{ $totalFeedbacks }}</p>
        </div>

        {{-- Overall Average Rating --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Overall Average Rating</p>
            <div class="flex items-center mt-1">
                <p class="text-3xl font-semibold text-yellow-700 mr-2">{{ number_format($averageRating, 1) }}</p>
                <div class="flex items-center">
                    {!! renderStars($averageRating) !!}
                </div>
            </div>
        </div>

        {{-- Action Required Count --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
            <p class="text-sm font-medium text-gray-500">Action Required (Rating &lt; 3)</p>
            <p class="mt-1 text-3xl font-semibold text-red-700">
                {{ $lowRatingCount }}
            </p>
        </div>
    </div>
    
    {{-- FACILITY PERFORMANCE BREAKDOWN --}}
    <section class="mt-8 bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Facility Performance Snapshot</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse ($facilityAverages as $facilityName => $data)
                <div class="p-4 rounded-lg bg-gray-50 border 
                    @if($data['average'] >= 4) border-green-300 
                    @elseif($data['average'] >= 3) border-yellow-300 
                    @else border-red-300 
                    @endif
                    ">
                    <p class="text-sm font-medium text-gray-700 capitalize">{{ $facilityLabels[$facilityName] ?? ucfirst($facilityName) }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-2xl font-bold 
                            @if($data['average'] >= 4) text-green-700 
                            @elseif($data['average'] >= 3) text-yellow-700 
                            @else text-red-700 
                            @endif
                        ">{{ number_format($data['average'], 1) }}</span>
                        <span class="text-xs text-gray-500">({{ $data['count'] }} FDBK)</span>
                    </div>
                </div>
            @empty
                    <p class="col-span-4 text-gray-500">No facility-specific data available yet.</p>
            @endforelse
        </div>
    </section>

    {{-- FEEDBACK LIST & FILTERS --}}
    <section class="mt-10" x-data="{ 
        // Data and State
        allFeedbacks: {{ json_encode($feedbacks) }},
        filterFacility: '', 
        filterRating: '', // Now represents the exact rating to filter by
        sortDirection: 'desc', 

        // Style/Label Mappings
        statusMap: {
            'new': { 'bg': 'bg-indigo-100', 'text': 'text-indigo-800' },
            'reviewed': { 'bg': 'bg-yellow-100', 'text': 'text-yellow-800' },
            'resolved': { 'bg': 'bg-green-100', 'text': 'text-green-800' },
            'ignored': { 'bg': 'bg-gray-100', 'text': 'text-gray-800' }
        },
        facilityLabels: {
            'discussion': 'Discussion Room',
            'center': 'Activity Center',
            'pad': 'Nap Pad',
            'other': 'Other Facility',
        },

        // Helpers
        getBadgeClasses(status) {
            const style = this.statusMap[status] || this.statusMap['new'];
            return `${style.bg} ${style.text}`;
        },

        getFacilityLabel(subject) {
            return this.facilityLabels[subject] || (subject.charAt(0).toUpperCase() + subject.slice(1));
        },
        
        renderStars(rating) {
            let html = '';
            const maxStars = 5;
            const fullStars = Math.floor(rating);
            const halfStar = rating - fullStars >= 0.5;
            
            for (let i = 1; i <= maxStars; i++) {
                if (i <= fullStars) {
                    html += '<span class=\'text-yellow-400 text-lg\'>★</span>';
                } else if (i === fullStars + 1 && halfStar) {
                    html += '<span class=\'text-yellow-300 text-lg\'>★</span>';
                } else {
                    html += '<span class=\'text-gray-300 text-lg\'>★</span>';
                }
            }
            return html;
        },

        // Computed Property (Filtering and Sorting Logic)
        get filteredFeedbacks() {
            let filtered = this.allFeedbacks;

            // 1. Filtering
            if (this.filterFacility) {
                filtered = filtered.filter(f => f.subject === this.filterFacility);
            }

            // Filter by EXACT Rating
            if (this.filterRating) {
                const exactRating = parseInt(this.filterRating);
                // We check if the integer part of the rating matches the selected value
                filtered = filtered.filter(f => parseInt(f.rating) === exactRating);
            }

            // 2. Sorting (by created_at string)
            filtered = filtered.sort((a, b) => {
                const dateA = new Date(a.created_at);
                const dateB = new Date(b.created_at);

                let comparison = 0;
                if (dateA > dateB) {
                    comparison = 1;
                } else if (dateA < dateB) {
                    comparison = -1;
                }
                
                return this.sortDirection === 'asc' ? comparison : comparison * -1;
            });

            return filtered;
        },
    }">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">Individual Feedback Entries</h2>

        {{-- Filtering and Sorting Controls --}}
        <div class="bg-white p-4 rounded-xl shadow-md mb-6 flex flex-col sm:flex-row gap-4">
            
            {{-- Filter by Facility --}}
            <div class="sm:w-1/3">
                <label for="facility-filter" class="block text-xs font-medium text-gray-700 mb-1">Filter by Facility</label>
                <select id="facility-filter" x-model="filterFacility" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach ($facilityOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter by Exact Rating (UPDATED) --}}
            <div class="sm:w-1/3">
                <label for="rating-filter" class="block text-xs font-medium text-gray-700 mb-1">Exact Rating Filter</label>
                <select id="rating-filter" x-model="filterRating" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Ratings</option>
                    <option value="5">Exactly 5 Stars</option>
                    <option value="4">Exactly 4 Stars</option>
                    <option value="3">Exactly 3 Stars</option>
                    <option value="2">Exactly 2 Stars</option>
                    <option value="1">Exactly 1 Star</option>
                </select>
            </div>
            
            {{-- Sort by Date --}}
            <div class="sm:w-1/3">
                <label for="sort-date" class="block text-xs font-medium text-gray-700 mb-1">Sort Order</label>
                <select id="sort-date" x-model="sortDirection" class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="desc">Newest First (Date)</option>
                    <option value="asc">Oldest First (Date)</option>
                </select>
            </div>

        </div>


        {{-- DYNAMIC FEEDBACK LIST (x-for) --}}
        <div class="space-y-4">
            {{-- Empty State --}}
            <template x-if="filteredFeedbacks.length === 0">
                <div class="p-8 text-center bg-white rounded-xl shadow-lg">
                    <p class="text-gray-600">No feedback entries match the current filters.</p>
                </div>
            </template>

            {{-- Feedback Card Loop --}}
            <template x-for="feedback in filteredFeedbacks" :key="feedback.id">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4" 
                    :class="{ 
                        'border-green-500': feedback.rating >= 4, 
                        'border-yellow-500': feedback.rating == 3,
                        'border-red-500': feedback.rating < 3
                    }" 
                    x-data="{ expanded: false }">
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        
                        {{-- LEFT: Rating & Facility --}}
                        <div class="flex-grow mb-3 md:mb-0">
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center">
                                    {{-- Use Alpine helper and x-html --}}
                                    <div x-html="renderStars(feedback.rating)"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700" x-text="'(' + feedback.rating + '/5)'">
                                </span>
                            </div>
                            <p class="text-lg font-bold text-gray-900 mt-1 capitalize" x-text="getFacilityLabel(feedback.subject)">
                            </p>
                            <p class="text-xs text-gray-500">
                                Submitted by Student ID: <span class="font-mono text-gray-700" x-text="feedback.student_id"></span>
                            </p>
                        </div>

                        {{-- RIGHT: Status Update Form --}}
                        <div class="flex-shrink-0 flex items-center space-x-4">
                            
                            {{-- Status Update Form (Simulated action) --}}
                            <form :action="'{{ route('staff.feedbacks.update_status', ['feedback' => 'ID_PLACEHOLDER']) }}'.replace('ID_PLACEHOLDER', feedback.id)" method="POST" class="flex items-center space-x-2">
                                @csrf
                                @method('PATCH')
                                <label :for="'status_' + feedback.id" class="text-sm font-medium text-gray-700 hidden sm:inline">Status:</label>
                                <select name="status" :id="'status_' + feedback.id" onchange="this.form.submit()" 
                                        :class="getBadgeClasses(feedback.status) + ' p-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500 transition duration-150'"
                                        :value="feedback.status">
                                    @foreach ($statuses as $value => $style)
                                        <option value="{{ $value }}" :selected="feedback.status === '{{ $value }}'"
                                            class="{{ $style['bg'] }} {{ $style['text'] }}">
                                            {{ $style['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            
                            {{-- Date Submitted --}}
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Submitted</p>
                                <p class="text-sm text-gray-600 font-semibold" x-text="feedback.created_at_formatted">
                                </p>
                            </div>
                        </div>

                    </div>
                    
                    {{-- MESSAGE --}}
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Message:</p>
                        
                        {{-- Truncated/Expanded Text --}}
                        <div :class="{ 'max-h-16 overflow-hidden': !expanded }" class="text-gray-800 leading-relaxed" x-text="feedback.message">
                        </div>

                        <template x-if="feedback.message && feedback.message.length > 200">
                            <button @click="expanded = !expanded" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                <span x-show="!expanded">Read More...</span>
                                <span x-show="expanded">Show Less</span>
                            </button>
                        </template>

                    </div>
                </div>
            </template>
        </div>
    </section>
</div>

@endsection