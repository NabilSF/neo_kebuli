@props(['primary' => null, 'secondary' => null])
<div class="flex flex-wrap items-center gap-3">
    @if($secondary)
        <div class="flex items-center gap-2">
            {{ $secondary }}
        </div>
    @endif
    
    @if($secondary && $primary)
        <div class="w-px h-6 bg-border hidden sm:block" aria-hidden="true"></div>
    @endif
    
    @if($primary)
        <div class="flex items-center gap-2">
            {{ $primary }}
        </div>
    @endif
    
    {{ $slot }}
</div>
