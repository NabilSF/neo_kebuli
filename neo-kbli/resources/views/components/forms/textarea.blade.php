@props([
    'disabled' => false,
    'readonly' => false,
    'error' => false,
    'success' => false,
    'rows' => 4,
])

@php
    $baseClasses = 'block w-full rounded-md sm:text-sm disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed transition-colors min-h-[44px]';
    
    $hasError = $error || ($attributes->has('name') && $errors->has($attributes->get('name')));
    
    $stateClasses = 'border-border bg-surface';
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

<textarea 
    rows="{{ $rows }}"
    {{ $disabled ? 'disabled' : '' }} 
    {{ $readonly ? 'readonly' : '' }} 
    {{ $attributes->merge(['class' => $classes]) }}
>{{ $slot }}</textarea>
