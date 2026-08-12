@props(['action' => false, 'selection' => false, 'expand' => false, 'number' => false])

@php
    $baseClasses = 'whitespace-nowrap text-sm text-slate-700';
    
    if ($action) {
        $baseClasses .= ' px-6 py-4 text-right font-medium';
    } elseif ($selection || $expand) {
        $baseClasses .= ' px-6 py-4 w-12 text-center';
    } elseif ($number) {
        $baseClasses .= ' px-6 py-4 text-right tabular-nums';
    } else {
        $baseClasses .= ' px-6 py-4';
    }
@endphp

<td {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{ $slot }}
</td>
