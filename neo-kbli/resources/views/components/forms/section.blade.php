@props(['title', 'description' => null, 'actions' => null, 'status' => null, 'metadata' => null, 'divider' => true])

<section {{ $attributes->merge(['class' => 'flex flex-col gap-6 w-full']) }}>
    <header class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-semibold text-slate-800">{{ $title }}</h2>
                @if($status)
                    <div class="shrink-0 mt-1 sm:mt-0">
                        {{ $status }}
                    </div>
                @endif
            </div>
            @if($description)
                <p class="text-sm text-muted mt-1">{{ $description }}</p>
            @endif
            @if($metadata)
                <div class="text-xs text-slate-500 mt-2">
                    {{ $metadata }}
                </div>
            @endif
        </div>
        
        @if($actions)
            <div class="shrink-0 flex items-center gap-3 w-full sm:w-auto">
                {{ $actions }}
            </div>
        @endif
    </header>
    
    <div class="flex flex-col gap-6 w-full">
        {{ $slot }}
    </div>
    
    @if($divider)
        <hr class="border-border mt-2" aria-hidden="true" />
    @endif
</section>
