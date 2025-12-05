@props(['href', 'active' => false, 'theme' => 'default'])

@php
    // --- Define Color Scheme Based on Theme Prop ---
    if ($theme === 'student') {
        // Dark Green Theme: White/Light Green text on dark background (#00563f)
        $baseText = 'text-green-300';
        $baseBorder = 'border-transparent';
        $baseHover = 'hover:text-white hover:border-green-300';
        
        $activeText = 'text-white';
        $activeBorder = 'border-white';
    } else {
        // Default (Staff) Theme: White background with blue accent (standard Laravel/Jetstream colors)
        $baseText = 'text-gray-500';
        $baseBorder = 'border-transparent';
        $baseHover = 'hover:text-gray-700 hover:border-gray-300';
        
        $activeText = 'text-gray-900';
        $activeBorder = 'border-indigo-400';
    }
    
    // --- Apply Classes ---
    $classes = ($active ?? false)
                ? "inline-flex items-center px-1 pt-1 border-b-2 {$activeBorder} text-sm font-medium leading-5 {$activeText} focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out"
                : "inline-flex items-center px-1 pt-1 border-b-2 {$baseBorder} text-sm font-medium leading-5 {$baseText} {$baseHover} focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out";
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    {{ $slot }}
</a>