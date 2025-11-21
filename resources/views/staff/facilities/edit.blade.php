@extends('layouts.staff')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Facility: {{ $facility->name }}</h2>
        <a href="{{ route('staff.facilities.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition duration-150">
            &larr; Back to Facilities List
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <!-- Use PUT method for updating resources -->
        <form action="{{ route('staff.facilities.update', $facility) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Facility Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Facility Name</label>
                    <input 
                        type="text" 
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('name') border-red-500 @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $facility->name) }}" 
                        required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">e.g., Discussion Room A, Nap Pad 3</p>
                </div>

                <!-- Facility Type -->
                <div class="mb-4">
                    <label for="type" class="block text-sm font-bold text-gray-700 mb-1">Facility Type</label>
                    <select 
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('type') border-red-500 @enderror" 
                        id="type" 
                        name="type" 
                        required>
                        <option value="">Select a Type</option>
                        <option value="room" {{ old('type', $facility->type) == 'room' ? 'selected' : '' }}>Discussion Room</option>
                        <option value="pad" {{ old('type', $facility->type) == 'pad' ? 'selected' : '' }}>Nap Pad</option>
                        <option value="equipment" {{ old('type', $facility->type) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Capacity -->
                <div class="mb-4">
                    <label for="capacity" class="block text-sm font-bold text-gray-700 mb-1">Capacity</label>
                    <input 
                        type="number" 
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('capacity') border-red-500 @enderror" 
                        id="capacity" 
                        name="capacity" 
                        value="{{ old('capacity', $facility->capacity) }}" 
                        min="1" 
                        required>
                    @error('capacity')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Max number of users for this facility.</p>
                </div>

                <!-- STATUS: Crucial for staff functionality -->
                <div class="mb-4">
                    <label for="status" class="block text-sm font-bold text-blue-600 mb-1">Facility Status</label>
                    <select 
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('status') border-red-500 @enderror" 
                        id="status" 
                        name="status" 
                        required>
                        <option value="available" {{ old('status', $facility->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="not available" {{ old('status', $facility->status) == 'not available' ? 'selected' : '' }}>Not Available</option>
                        <option value="under maintenance" {{ old('status', $facility->status) == 'under maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-red-500 mt-1">Changing the status to 'not available' or 'maintenance' will prevent student bookings.</p>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Description (Optional)</label>
                <textarea 
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('description') border-red-500 @enderror" 
                    id="description" 
                    name="description" 
                    rows="3">{{ old('description', $facility->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg shadow-md hover:bg-green-700 transition duration-150">Update Facility</button>
        </form>
    </div>
@endsection