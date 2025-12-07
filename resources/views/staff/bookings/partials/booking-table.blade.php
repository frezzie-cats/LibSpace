@php
    // IMPORTANT: Wrapping functions in `if (!function_exists)` prevents "Cannot redeclare function" errors 
    // when this partial is included multiple times on the same page (e.g., in different tabs).
    
    // Helper function to format time (e.g., 09:00:00 to 9:00 AM)
    if (!function_exists('formatTime')) {
        function formatTime($time) {
            return \Carbon\Carbon::parse($time)->format('g:i A');
        }
    }

    // Helper function to get the status badge class
    if (!function_exists('getStatusBadgeClass')) {
        function getStatusBadgeClass($status) {
            switch ($status) {
                case 'confirmed': return 'bg-blue-100 text-blue-800';
                case 'cancelled': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }
    }
@endphp

<div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-300">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked By</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 bg-gray-50"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($bookings as $booking)
                <tr class="@if($booking->status === 'cancelled') opacity-60 bg-red-50 @endif hover:bg-gray-50 transition duration-150">
                    <!-- Facility Name (Uses the 'facility' relationship) -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $booking->facility->name }}
                    </td>
                    <!-- Date -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                    </td>
                    <!-- Time Slot -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatTime($booking->start_time) }} - {{ formatTime($booking->end_time) }}
                    </td>
                    <!-- Booked By (Uses the 'user' relationship) -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $booking->user->name }}
                        <div class="text-xs text-gray-500">{{ $booking->user->email }}</div>
                    </td>
                    <!-- Status Badge -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full {{ getStatusBadgeClass($booking->status) }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>