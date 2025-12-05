@extends('layouts.student')

@section('title', 'Feedback Form')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            {{-- Updated font style and weight to match the bookings page --}}
            <h2 class="text-3xl font-bold text-gray-800">
                Share Your Feedback
            </h2>
            {{-- Updated paragraph color for better consistency --}}
            <p class="mt-3 text-lg text-gray-600">
                We'd love to hear your thoughts and suggestions
            </p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            {{-- REMOVED DUPLICATE: The session('success') message is now handled only in the main layout file. --}}

            {{-- Error Messages: Kept here to show validation errors specific to the form submission. --}}
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 m-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were {{ $errors->count() }} errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- FORM START: Using space-y-4 for tighter vertical spacing --}}
            <form method="POST" action="{{ route('student.feedbacks.store') }}" class="p-6 sm:p-8 space-y-4"> 
                @csrf


                {{-- Subject Field --}}
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Facility</label>
                    <div class="mt-1">
                        <select id="subject" 
                                name="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 @error('subject') border-red-300 @enderror"
                                required>
                            <option value="">Select a Facility</option>
                            <option value="discussion" {{ old('subject') == 'discussion' ? 'selected' : '' }}>Discussion Room</option>
                            <option value="center" {{ old('subject') == 'center' ? 'selected' : '' }}>Activity Center</option>
                            <option value="pad" {{ old('subject') == 'pad' ? 'selected' : '' }}>Nap Pad</option>
                            <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    @error('subject')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rating Field -->
                <div x-data="ratingWidget({{ old('rating', 0) }})">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Rating</label>
                    <!-- Stars -->
                    <div class="flex gap-1 mb-2 items-center">
                        <template x-for="star in 5" :key="star">
                            <button
                                type="button"
                                class="text-3xl transition cursor-pointer p-0 border-0 bg-transparent"
                                :class="hover >= star || selected >= star ? 'text-yellow-400 drop-shadow-lg' : 'text-gray-300'"
                                @mouseover="hover = star"
                                @mouseleave="hover = selected"
                                @click="select(star)"
                            >
                                ★
                            </button>
                        </template>
                    </div>
                    <!-- Hidden field for Laravel -->
                    <input type="hidden" name="rating" :value="selected">

                    @error('rating')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                        

                {{-- Message Field --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                    <div class="mt-1">
                        <textarea id="message" 
                                    name="message" 
                                    rows="6"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 @error('message') border-red-300 @enderror"
                                    placeholder="Please share your detailed feedback, suggestions, or concerns..."
                                    required>{{ old('message') }}</textarea>
                    </div>
                    @error('message')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div>
                    <button type="submit"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                        {{-- Using a comment/message bubble icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.882 9.882 0 01-3.6-1.125l-4.7 1.875c-.267.106-.5-.111-.5-.316V12.75a9 9 0 019-9c4.97 0 9 3.582 9 8z" />
                        </svg>
                        Submit Feedback
                    </button>
                </div>
            </form>
            {{-- FORM END --}}
        </div>

        {{-- Additional Info --}}
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Thank Avail you for helping us improve!
            </p>
        </div>
    </div>
    
    {{-- Alpine.js Rating Logic (Moved outside the form for clean flow) --}}
    <script>
        function ratingWidget(initial = 0) {
            return {
                selected: initial,
                hover: initial,

                select(star) {
                    this.selected = star;
                    this.hover = star;
                }
            }
        }
    </script>  
</div>
@endsection