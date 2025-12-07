@extends('layouts.student')
@section('content')

<header class="relative bg-white rounded-xl shadow-2xl overflow-hidden -mt-6 mb-12 -mx-6 sm:-mx-8 lg:-mx-12 xl:-mx-24">
    
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('/assets/library-hero.jpg');">
    </div>
    
    <div class="absolute inset-0 bg-gray-900 bg-opacity-70"></div>
    
    <div class="relative max-w-4xl mx-auto py-24 px-6 text-center">
        <h1 class="text-5xl font-extrabold text-white mb-4 tracking-tight sm:text-6xl">
            Your Space, Your Time
        </h1>
        <p class="text-xl text-green-200 mb-8">
            Seamlessly book the perfect study environment or essential room for your academic journey at the university library.
        </p>
        <a href="#facilities-showcase" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-full text-gray-900 bg-white hover:bg-gray-100 transition duration-300 shadow-xl transform hover:scale-[1.02]">
            Explore Facilities
            <i class="fas fa-arrow-down ml-3"></i>
        </a>
    </div>
</header>

<section id="facilities-showcase" class="py-12">
    
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-800">Our Dedicated Spaces</h2>
        <p class="text-lg text-gray-600 mt-2">Find the right environment for collaboration, rest, or productivity.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white shadow-xl rounded-xl border-t-4 border-green-500 hover:shadow-2xl transition duration-300 ease-in-out flex flex-col">
            <img src="{{ asset('assets/Discussion Room.jpg') }}" alt="Image of a discussion room" class="w-full h-auto rounded-t-xl object-cover">
            
            <div class="p-6 flex flex-col flex-grow text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-3">Discussion Rooms</h3>
                <p class="text-gray-600 mb-4 text-sm flex-grow">
                    Ideal for group projects, meetings, and collaborative study sessions.
                </p>
                {{-- Button pushed to the bottom --}}
                <div class="mt-auto"> 
                    <a href="{{ route('student.facilities.index', ['type' => 'room']) }}" class="inline-flex items-center text-sm font-semibold text-green-700 hover:text-green-900 transition duration-150">
                        <i class="fas fa-search mr-2"></i> View Details & Book
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-xl border-t-4 border-green-500 hover:shadow-2xl transition duration-300 ease-in-out flex flex-col">
            <img src="{{ asset('assets/Nap Pads.jpg') }}" alt="Image of nap pads" class="w-full h-auto rounded-t-xl object-cover">
            
            <div class="p-6 flex flex-col flex-grow text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-3">Nap Pads</h3>
                <p class="text-gray-600 mb-4 text-sm flex-grow">
                    Recharge between classes with a quick power nap. Private, quiet, and essential for long study days.
                </p>
                {{-- Button pushed to the bottom --}}
                <div class="mt-auto">
                    <a href="{{ route('student.facilities.index', ['type' => 'pad']) }}" class="inline-flex items-center text-sm font-semibold text-green-700 hover:text-green-900 transition duration-150">
                        <i class="fas fa-search mr-2"></i> View Details & Book
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-xl border-t-4 border-green-600 hover:shadow-2xl transition duration-300 ease-in-out flex flex-col">
            <img src="{{ asset('assets/Activity Center.jpg') }}" alt="Image of activity center" class="w-full h-auto rounded-t-xl object-cover">
            
            <div class="p-6 flex flex-col flex-grow text-center">
                <h3 class="text-xl font-bold text-gray-900 mb-3">Activity Center</h3>
                <p class="text-gray-600 mb-4 text-sm flex-grow">
                    The Activity Center is the venue for various events and student workshops.
                </p>
                {{-- Button pushed to the bottom --}}
                <div class="mt-auto">
                    <a href="{{ route('student.facilities.index', ['type' => 'venue']) }}" class="inline-flex items-center text-sm font-semibold text-green-700 hover:text-green-900 transition duration-150">
                        <i class="fas fa-search mr-2"></i> View Details & Book
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mt-12 p-8 bg-green-50 rounded-xl shadow-inner text-center">
    <h3 class="text-xl font-bold text-green-800 mb-3">Ready to Book?</h3>
    <p class="text-green-700 mb-4">Search for a specific room number.</p>
    <form action="{{ route('student.facilities.index') }}" method="GET" class="max-w-xl mx-auto flex space-x-2">
        <input type="search" name="search" placeholder="E.g., Room 301 or Projector"
               class="flex-1 px-4 py-3 border border-green-300 rounded-lg focus:ring-green-500 focus:border-green-500 shadow-sm"
               value="{{ request('search') }}">
        <button type="submit" class="px-5 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition duration-150">
            Search
        </button>
    </form>
</div>

@endsection