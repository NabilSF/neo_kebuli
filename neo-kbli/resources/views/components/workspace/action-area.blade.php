@props(['primary' => null, 'secondary' => null, 'context' => null, 'overflow' => null])
<div class="flex flex-wrap items-center gap-2 sm:gap-3">
    @if($context)
        <div class="flex items-center gap-2">
            {{ $context }}
        </div>
        <div class="w-px h-6 bg-border hidden sm:block" aria-hidden="true"></div>
    @endif
    
    @if($secondary)
        <div class="flex items-center gap-2">
            {{ $secondary }}
        </div>
    @endif
    
    @if($primary)
        <div class="flex items-center gap-2">
            {{ $primary }}
        </div>
    @endif
    
    @if($overflow)
        <div class="flex items-center">
            {{ $overflow }}
        </div>
    @endif
    
    {{ $slot }}
</div>
