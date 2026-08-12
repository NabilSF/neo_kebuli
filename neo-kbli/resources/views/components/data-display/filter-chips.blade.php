@props([
    'label' => null,
    'dismissible' => true,
])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-800']) }}>
    @if($label)
        <span class="text-slate-500 mr-1">{{ $label }}:</span>
    @endif
    {{ $slot }}
    @if($dismissible)
        <button type="button" class="ml-1.5 inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full text-slate-400 hover:bg-slate-200 hover:text-slate-500 focus:bg-slate-500 focus:text-white " aria-label="Hapus filter">
            <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8"><path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7" /></svg>
        </button>
    @endif
</span>
