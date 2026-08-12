@props([
    'align' => 'right',
    'width' => '48',
])

@php
    $alignmentClasses = [
        'left' => 'origin-top-left left-0',
        'top' => 'origin-top',
        'right' => 'origin-top-right right-0',
    ][$align] ?? 'origin-top-right right-0';

    $widthClasses = [
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
    ][$width] ?? $width;
@endphp

<div class="relative inline-block text-left" {{ $attributes }} x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $widthClasses }} rounded-md shadow-lg {{ $alignmentClasses }}    bg-surface origin-top-right" 
         role="menu" aria-orientation="vertical" tabindex="-1" style="display: none;">
        <div class="py-1" role="none">
            {{ $slot }}
        </div>
    </div>
</div>
