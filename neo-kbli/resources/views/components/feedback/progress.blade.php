@props([
    'percentage' => 0,
    'label' => null,
    'description' => null,
    'status' => null,
    'indeterminate' => false,
])

<div {{ $attributes->merge(['class' => 'w-full']) }} role="progressbar" aria-valuenow="{{ $indeterminate ? '' : $percentage }}" aria-valuemin="0" aria-valuemax="100">
    @if($label || $status)
        <div class="flex justify-between items-end mb-1">
            @if($label)
                <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
            @endif
            @if($status)
                <span class="text-sm font-medium text-slate-700">{{ $status }}</span>
            @elseif(!$indeterminate)
                <span class="text-sm font-medium text-slate-700">{{ $percentage }}%</span>
            @endif
        </div>
    @endif
    
    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden relative flex">
        @if($indeterminate)
            <div class="bg-primary h-2 rounded-full w-full animate-pulse"></div>
        @else
            <div class="bg-primary h-2 rounded-full transition-all duration-300 ease-out" style="width: {{ $percentage }}%"></div>
        @endif
    </div>
    
    @if($description)
        <p class="text-xs text-muted mt-1">{{ $description }}</p>
    @endif
</div>
