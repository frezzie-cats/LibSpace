@props(['href', 'active' => false, 'theme' => 'default'])

@php
    // --- Define Color Scheme Based on Theme Prop ---
    if ($theme === 'student') {
        // Dark Green Theme: White/Light Green text on dark background (#00563f)
        $baseText = 'text-green-300';
        $baseBorder = 'border-transparent';
        $baseHover = 'hover:text-white hover:border-white'; 
        
        $activeText = 'text-white';
        $activeBorder = 'border-white';
        $focusBorder = 'focus:border-white';
        
    } else {
        // Default (Staff) Theme: White background with blue accent (standard Laravel/Jetstream colors)
        $baseText = 'text-gray-500';
        $baseBorder = 'border-transparent';
        $baseHover = 'hover:text-gray-700 hover:border-gray-300';
        
        $activeText = 'text-gray-900';
        $activeBorder = 'border-indigo-400';
        $focusBorder = 'focus:border-indigo-700';
    }
    
    // --- Apply Classes ---
    // IMPORTANT CHANGE: Keeping py-4 for vertical positioning, but reducing horizontal padding to px-2 
    // to make the border length closer to the word length.
    $commonClasses = 'inline-flex items-center px-2 py-4 border-b-2 text-sm font-medium focus:outline-none transition duration-150 ease-in-out';

    $classes = ($active ?? false)
                ? "{$commonClasses} {$activeBorder} {$activeText} {$focusBorder}"
                : "{$commonClasses} {$baseBorder} {$baseText} {$baseHover} {$focusBorder}";
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    {{ $slot }}
</a>