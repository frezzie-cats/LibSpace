@props(['href' => '#', 'active' => false])

@php
    $classes = ($active ?? false)
                ? 'flex items-center p-3 rounded-lg text-white bg-blue-700/80 hover:bg-blue-600 transition duration-150 ease-in-out shadow-md'
                : 'flex items-center p-3 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    {{ $slot }}
</a>