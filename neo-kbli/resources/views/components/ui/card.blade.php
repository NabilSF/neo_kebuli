@props(['noShadow' => false, 'borderOnly' => false])

@php
    $classes = 'bg-surface rounded-lg overflow-hidden flex flex-col';
    if ($borderOnly) {
        $classes .= ' border border-border';
    } elseif (!$noShadow) {
        $classes .= ' shadow-sm border border-border';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($header))
        <div class="px-4 py-5 sm:px-6 border-b border-border bg-slate-50/50">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-4 sm:p-6 flex-1">
        {{ $slot }}
    </div>
    
    @if(isset($footer) || isset($actions))
        <div class="px-4 py-4 sm:px-6 border-t border-border bg-slate-50 flex items-center justify-between gap-4 flex-wrap">
            <div class="text-sm text-muted">
                {{ $footer ?? '' }}
            </div>
            @if(isset($actions))
                <div class="flex items-center gap-2 shrink-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
</div>
