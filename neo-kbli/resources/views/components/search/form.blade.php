@props(['query' => ''])

<form action="{{ route('search') }}" method="GET">
    <div class="flex shadow-sm rounded-xl overflow-hidden bg-white border border-gray-300 focus-within:border-primary-400 focus-within:ring-2 focus-within:ring-primary-100 transition-shadow">
        <div class="flex items-center pl-4 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" name="q" value="{{ $query }}" class="block w-full pl-3 pr-4 py-3.5 text-gray-900 placeholder-gray-400 outline-none focus:ring-0 border-none bg-white" placeholder="Masukkan kode atau kata kunci KBLI...">
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium px-6 py-3.5 transition-colors border-l border-gray-200">
            Cari
        </button>
    </div>
</form>
