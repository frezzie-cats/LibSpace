@props(['href' => '#', 'active' => false, 'theme' => 'default'])

@php
    // --- Define Color Scheme Based on Theme Prop ---
    if ($theme === 'student') {
        // Student Theme (Used in the dark green top bar)
        $activeClasses = 'border-green-400 text-white bg-[#004230] focus:border-green-700';
        $defaultClasses = 'text-green-300 hover:text-white hover:bg-[#007452] hover:border-green-500 focus:text-white focus:bg-[#007452] focus:border-green-500';
    } else {
        // Staff Theme (Default: Dark Gray and Blue Accents)
        $activeClasses = 'border-blue-400 text-blue-300 bg-gray-700 focus:border-blue-700';
        $defaultClasses = 'text-gray-400 hover:text-gray-200 hover:bg-gray-700 hover:border-gray-500 focus:text-gray-200 focus:bg-gray-700 focus:border-gray-500';
    }

    // --- Apply Classes ---
    $baseClasses = 'block w-full pl-3 pr-4 py-2 border-l-4 text-left text-base font-medium transition duration-150 ease-in-out focus:outline-none';

    $classes = ($active ?? false)
                ? $baseClasses . ' ' . $activeClasses
                : $baseClasses . ' border-transparent ' . $defaultClasses;

@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    {{ $slot }}
</a>