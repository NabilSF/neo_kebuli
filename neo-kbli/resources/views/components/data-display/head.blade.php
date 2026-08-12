@props(['sortable' => false, 'direction' => null])

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider whitespace-nowrap']) }}>
    @if($sortable)
        <button type="button" class="group inline-flex items-center gap-1    rounded">
            {{ $slot }}
            <span class="flex flex-col items-center">
                @if($direction === 'asc')
                    <svg class="h-3 w-3 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                    <span class="sr-only">Diurutkan menaik</span>
                @elseif($direction === 'desc')
                    <svg class="h-3 w-3 text-primary" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    <span class="sr-only">Diurutkan menurun</span>
                @else
                    <svg class="h-3 w-3 text-slate-400 group-hover:text-slate-600 opacity-50" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                @endif
            </span>
        </button>
    @else
        {{ $slot }}
    @endif
</th>
