@props([
    'status' => 'nonaktif',
    'dot' => true,
])

@php
    $baseClasses = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium';
    $variantClasses = \App\Support\UI\Theme::statusIndicatorVariant($status);
    $classes = $baseClasses . ' ' . $variantClasses;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <svg class="mr-1.5 h-2 w-2 text-current opacity-75" fill="currentColor" viewBox="0 0 8 8" aria-hidden="true">
            <circle cx="4" cy="4" r="3" />
        </svg>
    @endif
    {{ ucfirst($status) }}
</span>
