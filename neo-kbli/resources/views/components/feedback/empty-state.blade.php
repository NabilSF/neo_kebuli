@props([
    'icon' => null,
    'title' => 'Belum ada data',
    'description' => null,
    'recommendation' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8 sm:p-12 text-center rounded-lg border-2 border-dashed border-border bg-slate-50 w-full']) }}>
    @if(isset($illustration))
        <div class="mb-6 w-full max-w-xs mx-auto text-slate-300">
            {{ $illustration }}
        </div>
    @elseif($icon)
        <div class="mb-4 text-slate-400">
            {{ $icon }}
        </div>
    @endif
    
    <h3 class="text-lg font-medium text-slate-900 mb-1">{{ $title }}</h3>
    
    @if($description)
        <p class="text-sm text-slate-500 max-w-md mx-auto mb-2">{{ $description }}</p>
    @endif

    @if($recommendation)
        <p class="text-sm text-primary font-medium max-w-md mx-auto mb-6">{{ $recommendation }}</p>
    @elseif($description)
        <div class="mb-6"></div>
    @endif
    
    @if(isset($primaryAction) || isset($secondaryAction) || isset($action))
        <div class="flex flex-col sm:flex-row items-center gap-3">
            {{ $primaryAction ?? '' }}
            {{ $secondaryAction ?? '' }}
            {{ $action ?? '' }}
        </div>
    @endif
</div>
