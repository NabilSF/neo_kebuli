@props([
    'variant' => 'info', // info, success, warning, danger
    'title' => null,
    'description' => null,
])

<div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-surface shadow-lg  " role="alert" aria-live="assertive" aria-atomic="true" {{ $attributes }}>
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                @if($variant === 'success')
                    <svg class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @elseif($variant === 'danger')
                    <svg class="h-6 w-6 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @elseif($variant === 'warning')
                    <svg class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                @else
                    <svg class="h-6 w-6 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @endif
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                @if($title)
                    <p class="text-sm font-medium text-slate-900">{{ $title }}</p>
                @endif
                @if($description || $slot->isNotEmpty())
                    <p class="mt-1 text-sm text-slate-500">{{ $description ?? $slot }}</p>
                @endif
            </div>
            <div class="ml-4 flex flex-shrink-0">
                <button type="button" class="inline-flex rounded-md bg-surface text-slate-400 hover:text-slate-500    ">
                    <span class="sr-only">Tutup notifikasi</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            </div>
        </div>
    </div>
</div>
