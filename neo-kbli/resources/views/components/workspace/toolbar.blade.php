@props(['searchRegion' => null, 'filters' => null, 'bulkActions' => null])
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full bg-surface p-3 rounded-lg border border-border">
    <div class="flex flex-col sm:flex-row sm:items-center flex-1 gap-3 w-full">
        @if($searchRegion)
            <div class="flex-1 w-full sm:max-w-md">
                {{ $searchRegion }}
            </div>
        @endif
        
        @if($filters)
            <div class="w-full sm:w-auto">
                {{ $filters }}
            </div>
        @endif
    </div>
    
    <div class="flex items-center gap-3 shrink-0 flex-wrap">
        @if($bulkActions)
            <div class="mr-auto sm:mr-0">
                {{ $bulkActions }}
            </div>
        @endif
        
        {{ $slot }}
    </div>
</div>
