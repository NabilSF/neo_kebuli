@props(['query' => ''])

<form action="{{ route('search') }}" method="GET">
    <div class="flex shadow-sm rounded-xl overflow-hidden bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 focus-within:border-orange-500 transition-all">
        <div class="flex items-center pl-4 pointer-events-none">
            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" name="q" value="{{ $query }}" class="block w-full pl-3 pr-4 py-3.5 text-slate-900 dark:text-white placeholder-slate-400 border-none bg-white dark:bg-slate-800" placeholder="Masukkan kode atau kata kunci KBLI...">
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3.5 transition-colors border-l border-orange-500 flex-shrink-0">
            Cari
        </button>
    </div>
</form>
