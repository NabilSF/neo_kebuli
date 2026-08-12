@props([
    'variant' => 'info',
    'title' => null,
    'description' => null,
    'dismissible' => false,
])

@php
    $baseClasses = 'rounded-md border p-4 flex gap-3 relative';
    $variantClasses = \App\Support\UI\Theme::alertVariant($variant);
    $classes = $baseClasses . ' ' . $variantClasses;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert" aria-live="polite">
    @if(isset($icon))
        <div class="shrink-0">
            {{ $icon }}
        </div>
    @endif
    
    <div class="flex-1">
        @if($title)
            <h3 class="text-sm font-semibold">{{ $title }}</h3>
        @endif
        
        @if($description || $slot->isNotEmpty())
            <div class="mt-1 text-sm {{ $title ? 'opacity-90' : '' }}">
                {{ $description ?? $slot }}
            </div>
        @endif
        
        @if(isset($actions))
            <div class="mt-3 flex gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
    
    @if($dismissible)
        <button type="button" class="absolute top-4 right-4 p-1 rounded-md hover:bg-black/5     opacity-70 hover:opacity-100 transition-opacity" aria-label="Tutup pesan">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    @endif
</div>
