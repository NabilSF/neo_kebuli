@props([
    'size' => 'md',
    'srText' => 'Memuat data...',
])

@php
    $sizeClasses = \App\Support\UI\Theme::spinnerSize($size);
    $classes = 'animate-spin text-current ' . $sizeClasses;
@endphp

<div role="status" class="inline-flex items-center justify-center">
    <svg {{ $attributes->merge(['class' => $classes]) }} xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
    </svg>
    <span class="sr-only">{{ $srText }}</span>
</div>
