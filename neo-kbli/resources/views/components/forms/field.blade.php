@props(['id' => null, 'label' => null, 'helper' => null, 'error' => null, 'warning' => null, 'success' => null, 'info' => null, 'hint' => null, 'required' => false, 'characterCounter' => null])

@php
    $hasError = $error || ($attributes->has('name') && $errors->has($attributes->get('name')));
    $errorMessage = $error ?? ($hasError ? $errors->first($attributes->get('name')) : null);
@endphp

<div {{ $attributes->except(['name'])->merge(['class' => 'flex flex-col gap-1.5 w-full']) }}>
    @if($label || $hint)
        <div class="flex items-center justify-between">
            @if($label)
                <label @if($id) for="{{ $id }}" @endif class="block text-sm font-medium text-slate-700">
                    {{ $label }}
                    @if($required)
                        <span class="text-danger" aria-hidden="true">*</span>
                        <span class="sr-only">Wajib diisi</span>
                    @endif
                </label>
            @endif
            
            @if($hint)
                <span class="text-xs text-muted">{{ $hint }}</span>
            @endif
        </div>
    @endif

    <div class="relative w-full">
        {{ $slot }}
    </div>

    @if($errorMessage || $warning || $success || $info || $helper || $characterCounter)
        <div class="flex justify-between items-start gap-4 mt-1">
            <div class="flex-1">
                @if($errorMessage)
                    <p class="text-sm text-danger" role="alert">{{ $errorMessage }}</p>
                @elseif($warning)
                    <p class="text-sm text-warning" role="alert">{{ $warning }}</p>
                @elseif($success)
                    <p class="text-sm text-success" role="status">{{ $success }}</p>
                @elseif($info)
                    <p class="text-sm text-info" role="status">{{ $info }}</p>
                @elseif($helper)
                    <p class="text-sm text-muted">{{ $helper }}</p>
                @endif
            </div>
            
            @if($characterCounter)
                <div class="text-xs text-muted shrink-0 text-right min-w-[3rem]" aria-live="polite">
                    {{ $characterCounter }}
                </div>
            @endif
        </div>
    @endif
</div>
