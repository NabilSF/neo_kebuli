@props(['item', 'query' => ''])

<a href="{{ route('kbli.show', $item->code) }}" class="block bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-primary-300 transition-all group">
    <div class="flex flex-col md:flex-row md:items-start gap-4">
        
        <div class="flex-shrink-0">
            <x-kbli.badge :code="$item->code" />
        </div>
        
        <div class="flex-grow">
            <h2 class="text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors mb-2">{!! $query ? preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200">$1</mark>', e($item->name)) : e($item->name) !!}</h2>
            
            <!-- Hierarchy Breadcrumbs -->
            @if($item->section && $item->section->category)
            <div class="flex items-center text-xs text-gray-500 mt-2 flex-wrap gap-y-1">
                <span class="font-medium">{{ $item->section->category->name }}</span>
                <svg class="w-3 h-3 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span>{{ $item->section->name }}</span>
            </div>
            @endif
        </div>
        
    </div>
</a>
