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
                <div class="mt-auto">
                    <a href="{{ route('student.facilities.index', ['type' => 'venue']) }}" class="inline-flex items-center text-sm font-semibold text-green-700 hover:text-green-900 transition duration-150">
                        <i class="fas fa-search mr-2"></i> View Details & Book
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =================================== --}}
{{-- NEW SECTION 1: QUICK STATUS & RULES --}}
{{-- =================================== --}}
<section class="py-12 bg-gray-50">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-800">Your Booking Experience</h2>
        <p class="text-lg text-gray-600 mt-2">Everything you need to know for a successful reservation.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
        
        {{-- Feature 1: Key Rules Summary --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-indigo-500">
            
            {{-- MODIFIED: Centered Icon and Stacked Heading --}}
            <div class="text-center mb-4">
                <i class="fas fa-calendar-check text-4xl text-indigo-600 mb-2"></i>
                <h3 class="text-xl font-bold text-gray-900">Key Booking Rules</h3>
            </div>
            {{-- END MODIFIED --}}
            
            <ul class="space-y-3 text-gray-700 list-none pl-0">
                <li class="flex items-start">
                    <i class="fas fa-clock text-indigo-500 mt-1 mr-3 text-sm"></i>
                    <span>Strict Daily Window: Bookings are limited to today only.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-business-time text-indigo-500 mt-1 mr-3 text-sm"></i>
                    <span>Operating Hours: All facilities are available between 8:00 AM and 6:00 PM.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-user-clock text-indigo-500 mt-1 mr-3 text-sm"></i>
                    <span>Limit: You may only have one active booking per time slot.</span>
                </li>
            </ul>
        </div>

        {{-- Feature 2: View All Facilities Available --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500 text-center">
            <i class="fas fa-laptop-house text-4xl text-green-600 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-900 mb-3">View All Facilities</h3>
            <p class="text-gray-700 mb-5">
                See the library's full availability. Check all facilities and open time slots for today in one place.
            </p>

            <a href="{{ route('student.facilities.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-full text-white bg-green-600 hover:bg-green-700 transition duration-300 shadow-lg mt-2">
                View All Slots
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- =================================== --}}
{{-- REVISED SECTION 2: THREE HORIZONTAL TESTIMONIALS --}}
{{-- =================================== --}}
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800">What Students Say</h2>
            <p class="text-lg text-gray-600 mt-2">Hear from your peers about the ease and value of the booking system.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            {{-- Testimonial 1 --}}
            <div class="bg-indigo-50 p-6 rounded-xl shadow-xl border-t-4 border-indigo-500 flex flex-col justify-between">
                <blockquote class="text-sm italic text-gray-700 leading-relaxed">
                    <i class="fas fa-quote-left text-indigo-400 mr-1 text-xs"></i>
                    The new booking system is a lifesaver! I can secure the discussion room I need in two minutes flat. It saves so much time searching around the library.
                </blockquote>
                <div class="mt-4 text-sm font-semibold text-indigo-800 pt-2 border-t border-indigo-200">
                    - Hexcy, <span class="block text-xs font-normal text-gray-600">4th Year BSIT Student</span>
                </div>
            </div>

            {{-- Testimonial 2 --}}
            <div class="bg-indigo-50 p-6 rounded-xl shadow-xl border-t-4 border-indigo-500 flex flex-col justify-between">
                <blockquote class="text-sm italic text-gray-700 leading-relaxed">
                    <i class="fas fa-quote-left text-indigo-400 mr-1 text-xs"></i>
                    The Nap Pads feature is a game-changer for long study days. Knowing I can book a quick, private rest session makes the grind so much more manageable.
                </blockquote>
                <div class="mt-4 text-sm font-semibold text-indigo-800 pt-2 border-t border-indigo-200">
                    - Mino, <span class="block text-xs font-normal text-gray-600">2nd Year Mathematics Major</span>
                </div>
            </div>

            {{-- Testimonial 3 --}}
            <div class="bg-indigo-50 p-6 rounded-xl shadow-xl border-t-4 border-indigo-500 flex flex-col justify-between">
                <blockquote class="text-sm italic text-gray-700 leading-relaxed">
                    <i class="fas fa-quote-left text-indigo-400 mr-1 text-xs"></i>
                    The real-time availability is flawless. I no longer run the risk of showing up to a room only to find it fully occupied. Great interface!
                </blockquote>
                <div class="mt-4 text-sm font-semibold text-indigo-800 pt-2 border-t border-indigo-200">
                    - Zierie, <span class="block text-xs font-normal text-gray-600">4th Year BSIT Student</span>
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection