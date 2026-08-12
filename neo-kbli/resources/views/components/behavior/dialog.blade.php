@props([
    'variant' => 'information', // confirmation, warning, success, danger, information
    'title' => null,
    'description' => null,
])

@php
    $iconMap = [
        'confirmation' => '<svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'warning' => '<svg class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
        'success' => '<svg class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>',
        'danger' => '<svg class="h-6 w-6 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'information' => '<svg class="h-6 w-6 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    ];

    $bgMap = [
        'confirmation' => 'bg-primary/10',
        'warning' => 'bg-warning/10',
        'success' => 'bg-success/10',
        'danger' => 'bg-danger/10',
        'information' => 'bg-info/10',
    ];
@endphp

<x-behavior.modal size="md" :dismissible="false" {{ $attributes }}>
    <div class="sm:flex sm:items-start p-6">
        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full {{ $bgMap[$variant] }} sm:mx-0 sm:h-10 sm:w-10">
            {!! $iconMap[$variant] !!}
        </div>
        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
            <h3 class="text-base font-semibold leading-6 text-slate-900" id="modal-title">{{ $title }}</h3>
            <div class="mt-2">
                <p class="text-sm text-slate-500">{{ $description }}</p>
                {{ $slot }}
            </div>
        </div>
    </div>
    
    <x-slot name="footer">
        @if(isset($actions))
            {{ $actions }}
        @endif
    </x-slot>
</x-behavior.modal>
