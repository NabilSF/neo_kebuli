@props([
    'type' => 'text',
    'lines' => 1,
])

@php
    $baseClasses = 'animate-pulse bg-slate-200 rounded';
@endphp

@if($type === 'text')
    <div class="flex flex-col gap-2 w-full">
        @for($i = 0; $i < $lines; $i++)
            <div {{ $attributes->merge(['class' => $baseClasses . ' h-4 w-' . ($i === $lines - 1 && $lines > 1 ? '2/3' : 'full')]) }} aria-hidden="true"></div>
        @endfor
        <span class="sr-only">Memuat konten...</span>
    </div>
@elseif($type === 'avatar')
    <div {{ $attributes->merge(['class' => $baseClasses . ' h-10 w-10 rounded-full shrink-0']) }} aria-hidden="true"></div>
    <span class="sr-only">Memuat gambar profil...</span>
@elseif($type === 'card')
    <div {{ $attributes->merge(['class' => 'border border-border p-4 rounded-lg w-full flex flex-col gap-4']) }} aria-hidden="true">
        <div class="flex items-center gap-4">
            <div class="{{ $baseClasses }} h-12 w-12 rounded-full shrink-0"></div>
            <div class="flex flex-col gap-2 flex-1">
                <div class="{{ $baseClasses }} h-4 w-1/3"></div>
                <div class="{{ $baseClasses }} h-3 w-1/4"></div>
            </div>
        </div>
        <div class="flex flex-col gap-2 mt-2">
            <div class="{{ $baseClasses }} h-4 w-full"></div>
            <div class="{{ $baseClasses }} h-4 w-5/6"></div>
        </div>
    </div>
    <span class="sr-only">Memuat data kartu...</span>
@endif
