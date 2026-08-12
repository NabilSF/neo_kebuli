@props([
    'variant' => 'primary',
    'size' => 'md',
    'iconOnly' => false,
    'fullWidth' => false,
    'loading' => false,
])

@php
    $variantClasses = \App\Support\UI\Theme::buttonVariant($variant);
    $sizeClasses = \App\Support\UI\Theme::buttonSize($size, $iconOnly);
    
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed rounded-md';
    
    $classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses;
    if ($fullWidth) {
        $classes .= ' w-full';
    }
    if ($loading) {
        $classes .= ' cursor-wait';
    }
@endphp

<button {{ $attributes->merge(['class' => $classes, 'disabled' => (bool)$loading]) }}>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
        <span class="sr-only">Memuat...</span>
    @endif
    
    @if(isset($leftIcon) && !$loading)
        <span class="mr-2 -ml-1 flex-shrink-0" aria-hidden="true">{{ $leftIcon }}</span>
    @endif
    
    {{ $slot }}
    
    @if(isset($rightIcon))
        <span class="ml-2 -mr-1 flex-shrink-0" aria-hidden="true">{{ $rightIcon }}</span>
    @endif
</button>
