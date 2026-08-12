@props(['disabled' => false, 'error' => false])

@php
    $baseClasses = 'h-4 w-4 rounded border-border text-primary  disabled:opacity-50 disabled:cursor-not-allowed transition-colors';
    
    if ($error) {
        $baseClasses = 'h-4 w-4 rounded border-danger text-danger  disabled:opacity-50 disabled:cursor-not-allowed transition-colors';
    }
@endphp

<input type="checkbox" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $baseClasses]) }}>
