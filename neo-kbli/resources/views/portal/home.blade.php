@extends('layouts.guest')

@section('title', 'NEO KEBULI — Portal Klasifikasi Baku Lapangan Usaha Indonesia')

@section('content')

{{-- ============================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================ --}}
<section class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 text-center">
        <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-6">
            Pencarian Kode KBLI
        </h1>

        <p class="text-lg text-slate-600 dark:text-slate-300 leading-relaxed mb-10 max-w-2xl mx-auto">
            Cari kode KBLI berdasarkan produk utama atau nama kegiatan.
        </p>

        {{-- Search Bar --}}
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm focus-within:border-orange-500 transition-all">
                    <div class="flex items-center pl-4">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" aria-label="Kata kunci pencarian KBLI" class="block w-full pl-3 pr-4 py-4 text-slate-900 dark:text-white placeholder-slate-400 border-none bg-white dark:bg-slate-800" placeholder="Cari kode KBLI atau kata kunci...">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 font-bold transition-colors flex-shrink-0">
                        Cari
                    </button>
                </div>
            </form>

            <div class="mt-5 flex flex-wrap justify-center items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                <span>Populer:</span>
                @forelse($popularSearches as $search)
                    <a href="{{ route('search', ['q' => $search->keyword]) }}" class="text-orange-600 dark:text-orange-400 font-semibold hover:underline capitalize">{{ $search->keyword }}</a>
                    @if(!$loop->last)
                        <span class="text-slate-300 dark:text-slate-600">·</span>
                    @endif
                @empty
                    <span class="text-slate-400 italic">Belum ada pencarian.</span>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- ABOUT KBLI SECTION --}}
{{-- ============================================ --}}
<x-home.about />

{{-- ============================================ --}}
{{-- PUBLIC SERVICES SECTION --}}
{{-- ============================================ --}}
<section class="bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-3">Fitur NEO KEBULI</h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-xl mx-auto text-sm">Akses cepat ke berbagai layanan utama portal NEO KEBULI.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('search') }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 hover:border-orange-400 dark:hover:border-orange-500 transition-all text-center group">
                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-700 text-slate-500 dark:text-slate-300 rounded-lg flex items-center justify-center mx-auto mb-4 group-hover:text-orange-500 transition-colors border border-slate-200 dark:border-slate-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Pencarian KBLI</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Cari kode klasifikasi berdasarkan produk utama atau nama kegiatan utama.</p>
            </a>

            <a href="{{ route('application.create') }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 hover:border-orange-400 dark:hover:border-orange-500 transition-all text-center group">
                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-700 text-slate-500 dark:text-slate-300 rounded-lg flex items-center justify-center mx-auto mb-4 group-hover:text-orange-500 transition-colors border border-slate-200 dark:border-slate-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Pengajuan</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Ajukan usulan penambahan produk utama kedalam database Neo Kebuli.</p>
            </a>

            <a href="{{ route('tracking.index') }}" class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 hover:border-orange-400 dark:hover:border-orange-500 transition-all text-center group">
                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-700 text-slate-500 dark:text-slate-300 rounded-lg flex items-center justify-center mx-auto mb-4 group-hover:text-orange-500 transition-colors border border-slate-200 dark:border-slate-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Lacak Status</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">Pantau status permohonan Anda secara real-time kapan saja.</p>
            </a>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- KBLI STRUCTURE --}}
{{-- ============================================ --}}
<section class="bg-slate-50/60 dark:bg-slate-900 py-16 border-t border-slate-200/60 dark:border-slate-800" id="explore">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-950/80 px-3 py-1 rounded-full border border-orange-200 dark:border-orange-800/80 inline-block mb-3">Klasifikasi 2 Digit</span>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">Jelajahi Golongan Pokok KBLI</h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto text-sm leading-relaxed">Pilih Golongan Pokok di bawah ini untuk melihat struktur turunan golongan (3 digit), subgolongan (4 digit), hingga kelompok usaha (5 digit).</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($categories as $category)
            <a href="{{ route('kbli.show', ['code' => $category->code]) }}" class="group bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-xs hover:border-orange-500 dark:hover:border-orange-500 hover:shadow-md hover:bg-orange-50/20 dark:hover:bg-slate-700/50 transition-all duration-200 flex flex-col justify-between">
                <!-- Top Header: Badge & Level -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono font-bold text-sm bg-orange-50 dark:bg-orange-950/80 text-orange-600 dark:text-orange-400 px-2.5 py-1 rounded-md border border-orange-200/80 dark:border-orange-800/80">
                            Kode {{ $category->code }}
                        </span>
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2.5 py-0.5 rounded-full">
                            2 Digit
                        </span>
                    </div>

                    <!-- Title -->
                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-snug line-clamp-2 mt-2 mb-3">
                        {{ $category->name }}
                    </h3>
                </div>

                <!-- Bottom Footer Bar -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-medium">
                    <span class="group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        Lihat Turunan (3 Digit)
                    </span>
                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:text-orange-500 dark:group-hover:text-orange-400 transform group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            @empty
            <div class="col-span-3 text-center py-12 text-slate-500 dark:text-slate-400">Belum ada data Golongan Pokok KBLI.</div>
            @endforelse
        </div>
    </div>
</section>

@endsection

