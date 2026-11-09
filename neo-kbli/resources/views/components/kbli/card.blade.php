@props(['item', 'query' => ''])

@php
    $fullText = $item->name;
    if (!empty($item->description)) {
        if (mb_strtoupper(trim($item->name)) !== mb_strtoupper(trim($item->description))) {
            $fullText .= ', ' . $item->description;
        } else {
            $fullText = $item->description;
        }
    }
    $fullTextUpper = mb_strtoupper($fullText, 'UTF-8');

    if (!empty($query)) {
        $highlightedText = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 text-slate-900 font-bold px-0.5 rounded">$1</mark>', e($fullTextUpper));
    } else {
        $highlightedText = e($fullTextUpper);
    }
@endphp

<a href="{{ route('kbli.show', $item->code) }}" class="block bg-white border-b border-slate-200 py-4 px-5 hover:bg-orange-50/40 transition-colors group">
    <div class="space-y-1.5">
        <!-- 5-Digit Code Header -->
        <div class="flex items-center justify-between">
            <span class="font-mono font-extrabold text-base text-slate-900 group-hover:text-orange-600 transition-colors tracking-tight">
                {{ $item->code }}
            </span>
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded-full">
                5 Digit
            </span>
        </div>
        
        <!-- Products & Activities List -->
        <p class="text-xs font-semibold text-slate-600 leading-relaxed uppercase tracking-wide">
            {!! $highlightedText !!}
        </p>

        <!-- Hierarchy Context Badge -->
        @if($item->section && $item->section->category)
        <div class="flex items-center text-[11px] text-slate-400 pt-1 font-medium">
            <span>{{ $item->section->category->name }}</span>
            <svg class="w-3 h-3 mx-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span>{{ $item->section->name }}</span>
        </div>
        @endif
    </div>
</a>
