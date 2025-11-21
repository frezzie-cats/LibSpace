@extends('layouts.staff')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Facilities Management</h2>
        <a href="{{ route('staff.facilities.create') }}" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
            <i class="fas fa-plus mr-2"></i> Add New Facility
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            @if ($facilities->isEmpty())
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                    No facilities found. Click "Add New Facility" to get started.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($facilities as $facility)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $facility->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $facility->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($facility->type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $facility->capacity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusClasses = match ($facility->status) {
                                                'available' => 'bg-green-100 text-green-800',
                                                'under maintenance' => 'bg-yellow-100 text-yellow-800',
                                                'not available' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                            {{ ucfirst($facility->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-center">
                                        <!-- Edit Button -->
                                        <a href="{{ route('staff.facilities.edit', $facility) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition duration-150 mr-2" title="Edit Facility">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>

                                        <!-- Delete Button (using a form for POST method) -->
                                        <form action="{{ route('staff.facilities.destroy', $facility) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this facility? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-600 bg-red-50 hover:bg-red-100 transition duration-150" title="Delete Facility">
                                                <i class="fas fa-trash-alt mr-1"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection