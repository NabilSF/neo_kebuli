@props(['items', 'query' => ''])

<div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs divide-y divide-slate-100">
    @foreach($items as $item)
        <x-kbli.card :item="$item" :query="$query" />
    @endforeach
</div>
