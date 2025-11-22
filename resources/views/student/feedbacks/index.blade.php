@extends('layouts.student')

@section('title', 'Feedback Form')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                Share Your Feedback
            </h2>
            <p class="mt-3 text-lg text-gray-500">
                We'd love to hear your thoughts and suggestions
            </p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 m-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Error Messages --}}
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

            <form method="POST" action="{{ route('student.feedbacks.store') }}" class="p-6 sm:p-8 space-y-6">
                @csrf


                {{-- Subject Field --}}
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Facility</label>
                    <div class="mt-1">
                        <select id="subject" 
                                name="subject"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('subject') border-red-300 @enderror"
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
                    <label class="text-sm font-medium text-gray-700 mb-3">Rating</label>
                    <!-- Stars -->
                    <div class="flex flex-row-reverse gap-1 mb-4">
                        <template x-for="star in 5" :key="star">
                            <button
                                type="button"
                                class="text-3xl transition"
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
                        

                {{-- Message Field --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                    <div class="mt-1">
                        <textarea id="message" 
                                  name="message" 
                                  rows="6"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('message') border-red-300 @enderror"
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
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>

        {{-- Additional Info --}}
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Thank you for helping us improve!
            </p>
        </div>
    </div>
   
</div>
@endsection