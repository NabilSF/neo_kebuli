@props([
    'disabled' => false,
    'readonly' => false,
    'error' => false,
    'success' => false,
    'type' => 'text'
])

@php
    $baseClasses = 'appearance-none block w-full rounded-md sm:text-sm disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed transition-colors min-h-[44px]';
    
    $stateClasses = 'border-border';
    
    // Check old() input or provided error
    $hasError = $error || ($attributes->has('name') && $errors->has($attributes->get('name')));
    
    if ($hasError) {
        $stateClasses = 'border-danger text-danger';
    } elseif ($success) {
        $stateClasses = 'border-success text-success';
    }

    if ($readonly) {
        $baseClasses .= ' bg-slate-50';
        $stateClasses = 'border-border';
    }

    $classes = $baseClasses . ' ' . $stateClasses;
@endphp

<div class="relative flex w-full">
    @if(isset($prefix))
        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-border bg-slate-50 text-muted sm:text-sm min-h-[44px]">
            {{ $prefix }}
        </span>
    @endif
    
    <div class="relative flex-1">
        @if(isset($leadingIcon))
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                {{ $leadingIcon }}
            </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            {{ $disabled ? 'disabled' : '' }} 
            {{ $readonly ? 'readonly' : '' }} 
            {{ $attributes->merge([
                'class' => $classes . 
                    (isset($prefix) ? ' rounded-l-none' : '') . 
                    (isset($suffix) ? ' rounded-r-none' : '') .
                    (isset($leadingIcon) ? ' pl-10' : '') .
                    (isset($trailingIcon) ? ' pr-10' : '')
            ]) }}
        >
        
        @if(isset($trailingIcon))
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-muted">
                {{ $trailingIcon }}
            </div>
        @endif
    </div>

    @if(isset($suffix))
        <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-border bg-slate-50 text-muted sm:text-sm min-h-[44px]">
            {{ $suffix }}
        </span>
    @endif
</div>
