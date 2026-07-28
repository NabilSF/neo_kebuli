@extends('layouts.app')

@section('title', 'Neo KBLI — Portal Klasifikasi Baku Lapangan Usaha Indonesia')

@section('content')

{{-- ============================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================ --}}
<section class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 text-center">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight leading-tight mb-6">
            Pencarian Kode KBLI
        </h1>

        <p class="text-lg text-gray-600 leading-relaxed mb-10 max-w-2xl mx-auto">
            Cari klasifikasi lapangan usaha berdasarkan kode, nama kegiatan, atau kata kunci usaha untuk kebutuhan perizinan dan registrasi.
        </p>

        {{-- Search Bar --}}
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <div class="flex rounded-md overflow-hidden border border-gray-300 bg-white focus-within:border-primary-400 focus-within:ring-1 focus-within:ring-primary-500 transition-all">
                    <div class="flex items-center pl-4">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" aria-label="Kata kunci pencarian KBLI" class="block w-full pl-3 pr-4 py-4 text-gray-900 placeholder-gray-400 outline-none border-none bg-white" placeholder="Cari kode KBLI atau kata kunci...">
                    <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 font-semibold transition-colors flex-shrink-0">
                        Cari
                    </button>
                </div>
            </form>

            <div class="mt-5 flex flex-wrap justify-center items-center gap-3 text-sm text-gray-500">
                <span>Populer:</span>
                @forelse($popularSearches as $search)
                    <a href="{{ route('search', ['q' => $search->keyword]) }}" class="text-primary-600 hover:underline capitalize">{{ $search->keyword }}</a>
                    @if(!$loop->last)
                        <span class="text-gray-300">·</span>
                    @endif
                @empty
                    <span class="text-gray-400 italic">Belum ada pencarian.</span>
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
<section class="bg-gray-50 border-b border-gray-200 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Fitur Neo Kebuli</h2>
            <p class="text-gray-600 max-w-xl mx-auto text-sm">Akses cepat ke berbagai layanan utama portal Neo KBLI.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('search') }}" class="bg-white rounded-md p-6 border border-gray-200 hover:border-primary-200 hover:bg-gray-50 transition-all text-center group">
                <div class="w-12 h-12 bg-gray-50 text-gray-500 rounded-md flex items-center justify-center mx-auto mb-4 group-hover:text-primary-600 transition-colors border border-gray-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Pencarian KBLI</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Cari kode klasifikasi berdasarkan nama usaha atau kode numerik.</p>
            </a>

            <a href="{{ route('application.create') }}" class="bg-white rounded-md p-6 border border-gray-200 hover:border-primary-200 hover:bg-gray-50 transition-all text-center group">
                <div class="w-12 h-12 bg-gray-50 text-gray-500 rounded-md flex items-center justify-center mx-auto mb-4 group-hover:text-primary-600 transition-colors border border-gray-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Pengajuan</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Ajukan permohonan klasifikasi usaha secara daring dengan mudah.</p>
            </a>

            <a href="{{ route('tracking.index') }}" class="bg-white rounded-md p-6 border border-gray-200 hover:border-primary-200 hover:bg-gray-50 transition-all text-center group">
                <div class="w-12 h-12 bg-gray-50 text-gray-500 rounded-md flex items-center justify-center mx-auto mb-4 group-hover:text-primary-600 transition-colors border border-gray-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Lacak Status</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Pantau status permohonan Anda secara real-time kapan saja.</p>
            </a>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- KBLI STRUCTURE --}}
{{-- ============================================ --}}
<section class="bg-white py-12" id="explore">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Jelajahi Struktur KBLI</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-sm">Telusuri hierarki lengkap dari Kategori hingga kode KBLI spesifik.</p>
        </div>

        <div class="space-y-3">
            @forelse($categories as $category)
            <div x-data="{ expanded: false }" class="border border-gray-200 rounded-md overflow-hidden bg-white hover:bg-gray-50 transition-colors">
                <a href="{{ route('search', ['q' => $category->code]) }}" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors focus:outline-none">
                    <div class="flex items-center gap-4 text-left">
                        <span class="w-10 h-10 rounded-lg bg-gray-50 text-gray-700 flex items-center justify-center font-bold text-sm flex-shrink-0 border border-gray-200 group-hover:bg-primary-500 transition-colors">{{ $category->code }}</span>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">{{ $category->name }}</div>
                            <div class="text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider font-medium line-clamp-1">{{ $category->description }}</div>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">Belum ada data kategori KBLI.</div>
            @endforelse
        </div>
    </div>
</section>

@endsection

