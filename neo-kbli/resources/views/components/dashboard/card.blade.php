<div class="bg-white border border-gray-200 rounded-md p-5 {{ $attributes->get('class') }}">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ $title }}</p>
    <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
    @if(isset($description))
    <p class="text-xs text-gray-400 mt-1">{{ $description }}</p>
    @endif
</div>
