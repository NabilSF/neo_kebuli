@props([
    'variant' => 'neutral',
    'dot' => false,
])

@php
    $baseClasses = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold';
    $classes = $baseClasses . ' ' . \App\Support\UI\Theme::badgeVariant($variant);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <svg class="mr-1.5 h-2 w-2 {{ \App\Support\UI\Theme::badgeDot($variant) }} rounded-full" fill="currentColor" viewBox="0 0 8 8" aria-hidden="true">
            <circle cx="4" cy="4" r="3" />
        </svg>
    @endif
    {{ $slot }}
</span>
