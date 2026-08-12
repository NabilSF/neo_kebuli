@props([
    'isLast' => false,
    'date' => null,
    'time' => null,
    'user' => null,
    'action' => null,
    'variant' => 'info', // info, success, warning, danger, gray
])

@php
    $iconMap = [
        'info' => '<svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" /></svg>',
        'success' => '<svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>',
        'warning' => '<svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>',
        'danger' => '<svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>',
        'gray' => '<svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" /></svg>',
    ];

    $bgMap = [
        'info' => 'bg-info',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'danger' => 'bg-danger',
        'gray' => 'bg-slate-400',
    ];
    
    $variantKey = is_string($variant) ? $variant : 'info';
@endphp

<li>
    <div class="relative pb-8">
        @if(!$isLast)
            <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
        @endif
        <div class="relative flex space-x-3">
            <div>
                <span class="h-8 w-8 rounded-full flex items-center justify-center {{ $bgMap[$variantKey] ?? 'bg-slate-400' }}">
                    {!! $iconMap[$variantKey] ?? $iconMap['gray'] !!}
                </span>
            </div>
            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                <div>
                    <p class="text-sm text-slate-500">
                        {!! $action !!} <span class="font-medium text-slate-900">{{ $user }}</span>
                    </p>
                    @if(isset($slot) && $slot->isNotEmpty())
                        <div class="mt-2 text-sm text-slate-700">
                            {{ $slot }}
                        </div>
                    @endif
                </div>
                <div class="whitespace-nowrap text-right text-sm text-slate-500 flex flex-col">
                    <time datetime="{{ $date }}">{{ $date }}</time>
                    @if($time)
                        <span class="text-xs">{{ $time }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>
