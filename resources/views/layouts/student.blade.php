<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LibSpace') }} | Student</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome CDN (for icons) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 

    <!-- Scripts (Vite handles CSS and main app JS, including Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
</head>
<body class="font-sans antialiased bg-gray-50">

    <div class="min-h-screen bg-gray-50">
        <!-- Student Navigation Bar -->
        <nav x-data="{ open: false, dropdownOpen: false }" class="bg-white border-b border-gray-100 shadow-sm">
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and Main Links -->
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="text-gray-800 text-xl font-bold tracking-wider">
                                LibSpace <span class="bg-green-500 text-xs text-white px-2 py-0.5 rounded-full ml-1">Student</span>
                            </a>
                        </div>
                        
                        <!-- Navigation Links (Desktop) -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('student.facilities.index')" :active="request()->routeIs('student.facilities.index')">
                                Find Facilities
                            </x-nav-link>
                            <x-nav-link :href="route('student.bookings.index')" :active="request()->routeIs('student.bookings.index')">
                                My Bookings
                            </x-nav-link>
                            <x-nav-link href="#">
                                Give Feedback (TBA)
                            </x-nav-link>
                        </div>
                    </div>

                    <!-- Settings Dropdown (Desktop) -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <button @click="dropdownOpen = ! dropdownOpen" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-user-circle mr-2 text-lg"></i> 
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                            
                            <!-- Dropdown Content -->
                            <div 
                                x-show="dropdownOpen"
                                @click.outside="dropdownOpen = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                style="display: none;"
                            >
                                <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                                    <a href="{{ route('profile.edit') }}" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <i class="fas fa-user-cog mr-2"></i> Profile
                                    </a>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm leading-5 text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50 transition duration-150 ease-in-out">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Responsive Navigation Menu (Mobile) -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('student.facilities.index')" :active="request()->routeIs('student.facilities.index')">
                        Find Facilities
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student.bookings.index')" :active="request()->routeIs('student.bookings.index')">
                        My Bookings
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#">
                        Give Feedback (TBA)
                    </x-responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')">
                            Profile
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-red-500 hover:text-red-300">
                                Log Out
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Student Content Area -->
        <main class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Status / Success Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Content Slot for specific views -->
            @yield('content')
        </main>
    </div>
</body>
</html>