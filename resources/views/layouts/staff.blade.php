<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Staff Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJ6T90BaeT0Y72zO6F/8I9p0+O/oBv4e4f1k81t/Q3fGq23p8vW2eA/vK8nBqg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Scripts (Vite handles CSS and main app JS, including Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">

    <!-- Overall container: Added overflow-x-hidden to prevent horizontal scrolling on the page -->
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100 overflow-x-hidden">
        
        <!-- 1. Fixed Top Navigation Bar (Always h-16, bg-gray-800) -->
        <nav class="bg-gray-800 border-b border-gray-700 shadow-lg fixed top-0 w-full z-50 h-16">
            <div class="max-w-full mx-auto h-full flex justify-between">
                
                <!-- Left Section: Logo and Hamburger -->
                <div class="flex items-center bg-gray-800 px-4 sm:px-6 lg:w-64 lg:justify-center">
                    <!-- Hamburger Button (Mobile only) -->
                    <button @click="sidebarOpen = ! sidebarOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out lg:hidden mr-4">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': sidebarOpen, 'inline-flex': ! sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Logo (This element will be centered on desktop screens) -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('staff.dashboard') }}" class="text-white text-xl font-bold tracking-wider">
                            LibSpace <span class="bg-blue-600 text-xs text-white px-2 py-0.5 rounded-full ml-1">Staff</span>
                        </a>
                    </div>
                </div>
                
                <!-- Right Section: User Settings Dropdown (Pushed to the remaining right space) -->
                <div class="flex items-center px-4 sm:px-6 lg:px-8">
                    <div class="ml-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-gray-400 hover:text-white hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 transition duration-150 ease-in-out">
                                    <i class="fas fa-user-circle mr-2"></i> 
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            
                            <x-slot name="content">
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
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 2. Fixed Sidebar (Mobile drawer + Desktop lock) -->
        <div x-cloak
             x-bind:class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
             @click.away="sidebarOpen = false"
             
             {{-- Shared mobile/desktop classes --}}
             class="fixed left-0 z-40 w-3/4 max-w-xs bg-gray-900 overflow-y-auto transition transform ease-in-out duration-300 -translate-x-full 
                      
                      {{-- MOBILE: Full height from top to bottom, offset by top bar (pt-16) --}}
                      inset-y-0 pt-16 
                      
                      {{-- DESKTOP FIX: Fixed from top-16 (below navbar) to bottom-0 (full height) and full width --}}
                      lg:fixed lg:top-16 lg:bottom-0 lg:w-64 lg:translate-x-0 lg:block">

            <div class="p-4 pt-4 lg:pt-2"> 
                <p class="text-xs uppercase text-gray-400 font-semibold mb-4 tracking-wider">Management</p>

                <!-- Primary Sidebar Links -->
                <nav class="space-y-2">
                    <x-sidebar-link :href="route('staff.dashboard')" :active="request()->routeIs('staff.dashboard')">
                        <i class="fas fa-tachometer-alt mr-3 w-5"></i> Dashboard
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('staff.facilities.index')" :active="request()->routeIs('staff.facilities.*')">
                        <i class="fas fa-warehouse mr-3 w-5"></i> Facilities
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('staff.bookings.index')" :active="request()->routeIs('staff.bookings.*')">
                        <i class="fas fa-calendar-check mr-3 w-5"></i> Booking Overview
                    </x-sidebar-link>
                    
                    {{-- NEW: Feedback Review Link --}}
                    <x-sidebar-link :href="route('staff.feedbacks.index')" :active="request()->routeIs('staff.feedbacks.*')">
                        <i class="fas fa-comments mr-3 w-5"></i> Feedback Review
                    </x-sidebar-link>

                    {{-- NEW: Reports Link --}}
                    <x-sidebar-link :href="route('staff.reports.index')" :active="request()->routeIs('staff.reports.index')">
                        <i class="fas fa-chart-line mr-3 w-5"></i> Generate Reports
                    </x-sidebar-link>
                    
                </nav>
            </div>
        </div>
        
        <!-- Mobile Overlay Background (Optional, but good practice) -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black opacity-50 lg:hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-50" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-50" x-transition:leave-end="opacity-0"></div>

        <!-- 3. Main Content Area -->
        {{-- FIX: Removed w-full and added lg:w-[calc(100%-16rem)] to ensure the width exactly fills the remaining space after the sidebar offset (ml-64 = 16rem) --}}
        <main class="mt-16 p-4 sm:p-6 lg:p-8 lg:ml-64 lg:w-[calc(100%-16rem)]">
            
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