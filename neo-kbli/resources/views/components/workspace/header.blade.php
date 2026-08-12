@props([
    'title' => '',
    'subtitle' => null,
    'description' => null,
])

@php
    $subText = $subtitle ?? $description;
@endphp

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-5']) }}>
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $title }}</h1>
        @if($subText)
            <p class="text-sm text-slate-500 mt-1">{{ $subText }}</p>
        @endif
    </div>

    @if(isset($actions))
        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>
    @elseif(isset($slot) && $slot->isNotEmpty())
        <div class="flex items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
