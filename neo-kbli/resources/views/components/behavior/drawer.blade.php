@props([
    'position' => 'right', // left, right, top, bottom
    'title' => null,
    'dismissible' => true,
])

@php
    $positionClasses = [
        'right' => 'inset-y-0 right-0 max-w-md w-full border-l border-border',
        'left' => 'inset-y-0 left-0 max-w-md w-full border-r border-border',
        'top' => 'top-0 inset-x-0 h-96 w-full border-b border-border',
        'bottom' => 'bottom-0 inset-x-0 h-96 w-full border-t border-border',
    ][$position] ?? 'inset-y-0 right-0 max-w-md w-full border-l border-border';
@endphp

<div {{ $attributes->merge(['class' => 'fixed inset-0 overflow-hidden z-50']) }} aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 overflow-hidden">
        
        <div class="absolute inset-0 bg-slate-900/50 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

        <div class="fixed flex {{ $positionClasses }} bg-surface shadow-xl">
            <div class="h-full flex flex-col w-full">
                
                <div class="px-4 py-6 sm:px-6 border-b border-border flex items-center justify-between">
                    @if($title)
                        <h2 class="text-lg font-medium text-slate-900" id="slide-over-title">
                            {{ $title }}
                        </h2>
                    @endif
                    @if($dismissible)
                        <div class="ml-3 h-7 flex items-center">
                            <button type="button" class="bg-surface rounded-md text-slate-400 hover:text-slate-500    ">
                                <span class="sr-only">Tutup panel</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif
                </div>
                
                <div class="relative flex-1 px-4 py-6 sm:px-6 overflow-y-auto">
                    {{ $slot }}
                </div>
                
                @if(isset($footer))
                    <div class="border-t border-border px-4 py-4 sm:px-6 bg-slate-50">
                        {{ $footer }}
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>
