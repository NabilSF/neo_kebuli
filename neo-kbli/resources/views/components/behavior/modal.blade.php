@props([
    'size' => 'md',
    'title' => null,
    'description' => null,
    'dismissible' => true,
    'primaryAction' => null,
    'secondaryAction' => null,
])

@php
    $sizeClasses = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        'fullscreen' => 'sm:max-w-full sm:m-4 sm:h-[calc(100vh-2rem)]',
    ][$size] ?? 'sm:max-w-md';
@endphp

<div {{ $attributes->merge(['class' => 'fixed inset-0 z-50 overflow-y-auto']) }} aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-slate-900/50 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-surface rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $sizeClasses }} w-full flex flex-col">
            
            <div class="bg-surface px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-border">
                <div class="sm:flex sm:items-start justify-between">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        @if($title)
                            <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">
                                {{ $title }}
                            </h3>
                        @endif
                        @if($description)
                            <div class="mt-2">
                                <p class="text-sm text-slate-500">
                                    {{ $description }}
                                </p>
                            </div>
                        @endif
                    </div>
                    @if($dismissible)
                        <div class="ml-4 flex-shrink-0 flex">
                            <button type="button" class="bg-surface rounded-md text-slate-400 hover:text-slate-500    ">
                                <span class="sr-only">Tutup modal</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="px-4 py-5 sm:p-6 flex-1 overflow-y-auto">
                {{ $slot }}
            </div>
            
            @if(isset($footer) || $primaryAction || $secondaryAction)
                <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-border gap-2">
                    @if(isset($footer))
                        {{ $footer }}
                    @else
                        @if($primaryAction)
                            {{ $primaryAction }}
                        @endif
                        @if($secondaryAction)
                            {{ $secondaryAction }}
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
