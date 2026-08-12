@props(['title' => null])

<x-ui.card {{ $attributes }}>
    @if($title || isset($header))
        <x-slot name="header">
            @if(isset($header))
                {{ $header }}
            @else
                <h3 class="text-lg font-medium text-slate-900">{{ $title }}</h3>
            @endif
        </x-slot>
    @endif
    
    <dl class="divide-y divide-border">
        {{ $slot }}
    </dl>
</x-ui.card>
