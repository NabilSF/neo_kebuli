@props(['title', 'description' => null, 'breadcrumbs' => null, 'actions' => null, 'metadata' => null, 'status' => null])
<div class="mb-6 flex flex-col gap-4">
    @if($breadcrumbs)
        <div class="mb-2">
            {{ $breadcrumbs }}
        </div>
    @endif
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-800 leading-tight">
                    {{ $title ?? $slot }}
                </h1>
                @if($description)
                    <p class="text-sm text-muted mt-1">{{ $description }}</p>
                @endif
            </div>
            @if($status)
                <div class="mt-1 sm:mt-0">
                    {{ $status }}
                </div>
            @endif
        </div>
        
        @if($actions)
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>
    
    @if($metadata)
        <div class="mt-2 text-sm text-muted">
            {{ $metadata }}
        </div>
    @endif
</div>
