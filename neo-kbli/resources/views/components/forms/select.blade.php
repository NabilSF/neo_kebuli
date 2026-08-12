@props(['disabled' => false, 'error' => false, 'success' => false])

@php
    $baseClasses = 'block w-full rounded-md sm:text-sm disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed transition-colors py-2 pl-3 pr-10 appearance-none bg-surface min-h-[44px]';
    
    $hasError = $error || ($attributes->has('name') && $errors->has($attributes->get('name')));
    $stateClasses = 'border-border';
    
    if ($hasError) {
        $stateClasses = 'border-danger text-danger';
    } elseif ($success) {
        $stateClasses = 'border-success text-success';
    }

    $classes = $baseClasses . ' ' . $stateClasses;
@endphp

<div class="relative">
    <select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </select>
    
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </div>
</div>
