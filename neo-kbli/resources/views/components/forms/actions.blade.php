@props(['primary' => null, 'secondary' => null, 'destructive' => null, 'additional' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 w-full']) }}>
    @if($destructive)
        <div class="w-full sm:w-auto sm:mr-auto">
            {{ $destructive }}
        </div>
    @endif

    @if($additional)
        <div class="w-full sm:w-auto flex items-center gap-2">
            {{ $additional }}
        </div>
    @endif
    
    @if($secondary)
        <div class="w-full sm:w-auto flex items-center gap-2">
            {{ $secondary }}
        </div>
    @endif

    @if($primary)
        <div class="w-full sm:w-auto flex items-center gap-2">
            {{ $primary }}
        </div>
    @endif
</div>
