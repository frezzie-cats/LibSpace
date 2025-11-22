<x-staff-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Booking Details: #{{ $booking->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="p-6 bg-indigo-600 text-white flex justify-between items-center">
                    <h3 class="text-2xl font-bold">Reservation Summary</h3>
                    <span class="px-3 py-1 bg-indigo-800 rounded-full text-sm font-medium uppercase">
                        {{ $booking->status }}
                    </span>
                </div>

                <div class="p-6 space-y-6">
                    
                    <!-- Booking Information -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-700 mb-3 border-l-4 border-indigo-500 pl-3">Time & Date</h4>
                        <div class="grid grid-cols-2 gap-4 text-gray-600">
                            <div>
                                <p class="font-medium">Booking Date:</p>
                                <p class="text-gray-900 text-xl font-bold">{{ \Carbon\Carbon::parse($booking->booking_date)->format('F jS, Y') }}</p>
                            </div>
                            <div>
                                <p class="font-medium">Time Slot:</p>
                                <p class="text-gray-900 text-xl font-bold">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Facility Details -->
                    <div class="border-b pb-4">
                        <h4 class="text-lg font-semibold text-gray-700 mb-3 border-l-4 border-green-500 pl-3">Facility Details</h4>
                        <div class="space-y-2">
                            <p class="text-2xl font-bold text-gray-900">{{ $booking->facility->name }}</p>
                            <p class="text-gray-600">{{ $booking->facility->description }}</p>
                            <p class="text-sm text-gray-500">
                                **Type:** {{ ucfirst($booking->facility->type) }} |
                                **Capacity:** {{ $booking->facility->capacity }} people
                            </p>
                            <a href="{{ route('staff.facilities.edit', $booking->facility) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View/Manage Facility Details &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Student Details -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-3 border-l-4 border-yellow-500 pl-3">Student Information</h4>
                        <div class="space-y-2">
                            <p class="text-xl font-bold text-gray-900">{{ $booking->user->name }}</p>
                            <p class="text-gray-600">
                                <span class="font-medium">Email:</span> {{ $booking->user->email }}
                            </p>
                            <p class="text-sm text-gray-500">
                                **User ID:** {{ $booking->user->id }}
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Footer/Actions -->
                <div class="p-6 bg-gray-50 border-t flex justify-end">
                    {{-- 
                        Future features: 
                        - Add buttons here for staff actions like 'Cancel Booking', 'Check-In', etc.
                        - For now, staff simply views the data.
                    --}}
                    <a href="{{ route('staff.bookings.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-500 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Back to All Bookings
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-staff-layout>