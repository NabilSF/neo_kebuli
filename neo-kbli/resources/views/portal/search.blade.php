@extends('layouts.app')

@section('title', $query ? "Hasil Pencarian untuk '" . e($query) . "' - Neo KBLI" : 'Cari Database KBLI')

@section('content')
<div class="bg-gray-50 py-10 border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Search Bar -->
        <div class="max-w-3xl mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Cari Database KBLI</h1>
            <x-search.form :query="$query" />
        </div>

        @if($query)
            <div class="text-sm text-gray-500">
                Ditemukan <span class="font-bold text-gray-900">{{ $items->total() }}</span> hasil untuk "<span class="font-bold text-gray-900">{{ $query }}</span>"
            </div>
        @endif

    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow">
    
    @if($items->count() > 0)
        <x-search.result-list :items="$items" :query="$query" />

        <div class="mt-8">
            {{ $items->withQueryString()->links() }}
        </div>
    @else
        <!-- Empty State -->
        <x-search.empty-state :query="$query" />
    @endif

</div>
@endsection
