@props(['label', 'value' => null, 'status' => null, 'metadata' => null])

<div {{ $attributes->merge(['class' => 'py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6']) }}>
    <dt class="text-sm font-medium text-slate-500 flex flex-col justify-center">
        {{ $label }}
        @if($metadata)
            <span class="text-xs text-slate-400 mt-1">{{ $metadata }}</span>
        @endif
    </dt>
    <dd class="mt-1 text-sm text-slate-900 sm:mt-0 sm:col-span-2 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-2 flex-wrap">
            @if(isset($badge))
                {{ $badge }}
            @endif
            @if($status)
                <x-feedback.status-indicator :status="$status" />
            @endif
            {{ $value ?? $slot }}
        </div>
        
        @if(isset($action))
            <div class="shrink-0">
                {{ $action }}
            </div>
        @endif
    </dd>
</div>
