@props(['items' => []])
<nav aria-label="Penunjuk arah" class="flex items-center text-sm text-muted">
    <ol class="flex items-center space-x-2">
        @foreach($items as $item)
            <li class="flex items-center">
                @if(!$loop->first)
                    <svg class="w-4 h-4 mx-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @endif
                
                @if(!$loop->last && isset($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors duration-150 flex items-center">
                        @if(isset($item['icon']))
                            <span class="mr-1" aria-hidden="true">{!! $item['icon'] !!}</span>
                        @endif
                        {{ $item['label'] }}
                    </a>
                @else
                    <span aria-current="page" class="font-medium text-slate-700 flex items-center">
                        @if(isset($item['icon']))
                            <span class="mr-1" aria-hidden="true">{!! $item['icon'] !!}</span>
                        @endif
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
