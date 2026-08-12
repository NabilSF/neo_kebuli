@props(['selected' => false])

<tr {{ $attributes->merge(['class' => 'hover:bg-slate-50 transition-colors ' . ($selected ? 'bg-primary/5' : 'bg-surface')]) }}>
    {{ $slot }}
</tr>
