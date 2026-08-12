@props(['disabled' => false, 'error' => false])

@php
    $hasError = $error || ($attributes->has('name') && $errors->has($attributes->get('name')));
@endphp

<label class="relative inline-flex items-center cursor-pointer min-h-[44px]">
    <input type="checkbox" class="peer sr-only" {{ $attributes }} {{ $disabled ? 'disabled' : '' }}>
    <div class="w-11 h-6 bg-slate-200 peer- peer- peer- peer- rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[12px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary {{ $hasError ? '  ' : '' }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
    @if($slot->isNotEmpty())
        <span class="ml-3 text-sm font-medium text-slate-700 {{ $disabled ? 'opacity-50' : '' }}">{{ $slot }}</span>
    @endif
</label>
