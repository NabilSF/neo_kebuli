@props(['items', 'query' => ''])

<div class="space-y-4">
    @foreach($items as $item)
        <x-kbli.card :item="$item" :query="$query" />
    @endforeach
</div>
