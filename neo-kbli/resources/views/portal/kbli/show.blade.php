@extends('layouts.guest')

@section('title', "KBLI {$item->code} - {$item->name}")
@section('meta_description', Str::limit(strip_tags($item->description), 150))

@section('content')
<!-- Header & Breadcrumbs -->
<div class="bg-gray-50 border-b border-gray-200 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 flex-wrap">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Beranda
                    </a>
                </li>
                @if($item->section && $item->section->category)
                <li>
                    <div class="flex items-center text-xs">
                        <svg class="w-3.5 h-3.5 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('home') }}#explore" class="hover:text-primary-600 transition-colors">Kategori {{ $item->section->category->code }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center text-xs">
                        <svg class="w-3.5 h-3.5 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-600">Bagian {{ $item->section->code }}</span>
                    </div>
                </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center text-xs font-semibold">
                        <svg class="w-3.5 h-3.5 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200 font-mono">{{ $item->code }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-start gap-4">
            <div class="flex-shrink-0">
                <span class="inline-flex bg-orange-500 text-white text-2xl font-bold px-3 py-1.5 rounded-md border border-orange-600 shadow-xs">
                    {{ $item->code }}
                </span>
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-2">{{ $item->name }}</h1>
                <div class="flex flex-wrap items-center gap-3 mt-4">
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        
        <!-- Left: Main Description -->
        <div class="md:col-span-2 space-y-10">
            
            <section>
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Deskripsi
                </h2>
                <div class="prose prose-lg text-gray-700 max-w-none">
                    {!! nl2br(e($item->description)) !!}
                </div>
            </section>

        </div>

        <!-- Right: Meta Data Sidebar -->
        <div class="space-y-6">
            
            <!-- Compact Hierarchy Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2.5">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Hierarki Klasifikasi KBLI
                    </h3>
                    <span class="text-[10px] font-semibold px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">5 Tingkat</span>
                </div>
                
                <div class="space-y-2">
                    <!-- Level 1: Kategori -->
                    @if($item->section && $item->section->category)
                    <a href="{{ route('kbli.show', ['code' => substr($item->code, 0, 2)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950 transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-slate-400 flex-shrink-0"></span>
                            <span class="font-medium text-slate-900 group-hover:text-orange-950 truncate">{{ $item->section->category->name }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded ml-1 flex-shrink-0">Kat {{ $item->section->category->code }}</span>
                    </a>
                    @endif

                    <!-- Level 2: Gol. Pokok -->
                    <a href="{{ route('kbli.show', ['code' => substr($item->code, 0, 2)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950 transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>
                            <span class="font-medium text-slate-900 group-hover:text-orange-950 truncate">{{ \App\Helpers\Kbli2025Helper::getName(substr($item->code, 0, 2)) }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded ml-1 flex-shrink-0">2 Digit: {{ substr($item->code, 0, 2) }}</span>
                    </a>

                    <!-- Level 3: Golongan -->
                    <a href="{{ route('kbli.show', ['code' => substr($item->code, 0, 3)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950 transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-400 flex-shrink-0"></span>
                            <span class="font-medium text-slate-900 group-hover:text-orange-950 truncate">{{ \App\Helpers\Kbli2025Helper::getName(substr($item->code, 0, 3)) }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded ml-1 flex-shrink-0">3 Digit: {{ substr($item->code, 0, 3) }}</span>
                    </a>

                    <!-- Level 4: Subgolongan -->
                    <a href="{{ route('kbli.show', ['code' => substr($item->code, 0, 4)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950 transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400 flex-shrink-0"></span>
                            <span class="font-medium text-slate-900 group-hover:text-orange-950 truncate">{{ \App\Helpers\Kbli2025Helper::getName(substr($item->code, 0, 4)) }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold bg-purple-100 text-purple-800 px-2 py-0.5 rounded ml-1 flex-shrink-0">4 Digit: {{ substr($item->code, 0, 4) }}</span>
                    </a>

                    <!-- Level 5: Kelompok (Aktif) -->
                    <div class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-orange-50 border border-orange-300 text-orange-950 font-semibold shadow-2xs">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500 ring-2 ring-orange-200 flex-shrink-0"></span>
                            <span class="truncate">{{ $item->name }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-extrabold bg-orange-500 text-white px-2 py-0.5 rounded ml-1 flex-shrink-0">{{ $item->code }}</span>
                    </div>
                </div>

                <!-- Expandable Guidelines Toggle -->
                <details class="group border-t border-slate-100 mt-3 pt-2 text-xs text-slate-500">
                    <summary class="cursor-pointer font-medium text-slate-600 hover:text-orange-600 flex items-center justify-between select-none py-1 transition-colors">
                        <span class="flex items-center text-[11px]">
                            <svg class="w-3.5 h-3.5 mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Penjelasan Struktur Digit
                        </span>
                        <svg class="w-3.5 h-3.5 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="mt-2 space-y-1.5 pl-2 text-[11px] text-slate-600 border-l-2 border-orange-300 bg-orange-50/50 p-2.5 rounded-r">
                        <p><strong class="text-slate-800">2 Digit (Gol. Pokok):</strong> Mengelompokkan kegiatan sejenis.</p>
                        <p><strong class="text-slate-800">3 Digit (Golongan):</strong> Merinci jenis aktivitas.</p>
                        <p><strong class="text-slate-800">4 Digit (Subgolongan):</strong> Merinci kegiatan dalam golongan.</p>
                        <p><strong class="text-slate-800">5 Digit (Kelompok):</strong> Jenis usaha spesifik (paling rinci).</p>
                    </div>
                </details>
            </div>

            <!-- Call to Action -->
            <div class="bg-white border border-orange-200 rounded-xl p-6 text-center relative overflow-hidden shadow-md mt-8">
                <div class="relative z-10">
                    <h3 class="font-extrabold text-xl mb-2 text-slate-900">Siap Mengajukan?</h3>
                    <p class="text-xs text-black font-semibold mb-5 leading-relaxed">Mulai proses pendaftaran bisnis Anda menggunakan kode KBLI ini secara cepat & aman.</p>
                    <a href="{{ route('application.create', ['kbli' => $item->code]) }}" class="inline-flex items-center justify-center w-full text-center bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-bold py-3 px-5 rounded-lg shadow-md hover:shadow-orange-500/20 transition-all border border-orange-400 text-sm tracking-wide">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        Ajukan Sekarang
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
