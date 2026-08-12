@props([
    'title',
    'value' => null,
    'description' => null,
    'trend' => null, // 'up', 'down'
    'trendValue' => null,
    'status' => null,
    'loading' => false,
    'empty' => false,
    'error' => false,
])

<x-ui.card {{ $attributes }}>
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-medium text-slate-500 truncate">{{ $title }}</h3>
            
            <div class="mt-2 flex items-baseline gap-2 flex-wrap min-h-[2rem]">
                @if($loading)
                    <x-feedback.skeleton type="text" lines="1" class="h-8 w-24" />
                @elseif($error)
                    <span class="text-sm font-medium text-danger">Data gagal dimuat</span>
                @elseif($empty)
                    <span class="text-sm font-medium text-slate-400">Belum ada data</span>
                @else
                    <span class="text-2xl sm:text-3xl font-bold text-slate-900 truncate">{{ $value ?? $slot }}</span>
                @endif
                
                @if($status && !$loading && !$error && !$empty)
                    <span class="ml-2 shrink-0">
                        {{ $status }}
                    </span>
                @endif
            </div>
        </div>
        
        @if(isset($icon))
            <div class="p-3 bg-slate-50 rounded-lg text-slate-400 shrink-0">
                {{ $icon }}
            </div>
        @endif
    </div>
    
    @if(($description || $trend) && !$loading && !$error && !$empty)
        <div class="mt-4 flex items-center gap-2 text-sm flex-wrap">
            @if($trend === 'up')
                <span class="flex items-center text-success font-medium shrink-0">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <span class="sr-only">Naik</span>
                    {{ $trendValue }}
                </span>
            @elseif($trend === 'down')
                <span class="flex items-center text-danger font-medium shrink-0">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                    <span class="sr-only">Turun</span>
                    {{ $trendValue }}
                </span>
            @endif
            
            @if($description)
                <span class="text-slate-500">{{ $description }}</span>
            @endif
        </div>
    @endif
    
    @if(isset($action))
        <div class="mt-4 pt-4 border-t border-border">
            {{ $action }}
        </div>
    @endif
</x-ui.card>
