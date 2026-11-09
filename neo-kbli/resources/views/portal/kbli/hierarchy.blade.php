@extends('layouts.guest')

@section('title', "KBLI {$code} - {$title}")
@section('meta_description', "Daftar turunan klasifikasi KBLI kode {$code} - {$title}")

@section('content')
<!-- Header & Breadcrumbs -->
<div class="bg-slate-50 border-b border-slate-200/80 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Dynamic Breadcrumbs -->
        <nav class="flex text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 flex-wrap">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center hover:text-orange-600 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Beranda
                    </a>
                </li>

                @if($len >= 2)
                <li>
                    <div class="flex items-center text-xs">
                        <svg class="w-3.5 h-3.5 text-slate-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('kbli.show', ['code' => substr($code, 0, 2)]) }}" class="hover:text-orange-600 transition-colors truncate max-w-[200px]">
                            {{ \App\Helpers\Kbli2025Helper::getName(substr($code, 0, 2)) }}
                        </a>
                    </div>
                </li>
                @endif

                @if($len >= 3)
                <li>
                    <div class="flex items-center text-xs">
                        <svg class="w-3.5 h-3.5 text-slate-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('kbli.show', ['code' => substr($code, 0, 3)]) }}" class="hover:text-orange-600 transition-colors truncate max-w-[200px]">
                            {{ \App\Helpers\Kbli2025Helper::getName(substr($code, 0, 3)) }}
                        </a>
                    </div>
                </li>
                @endif

                @if($len >= 4)
                <li>
                    <div class="flex items-center text-xs">
                        <svg class="w-3.5 h-3.5 text-slate-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-slate-600 truncate max-w-[200px]">{{ \App\Helpers\Kbli2025Helper::getName(substr($code, 0, 4)) }}</span>
                    </div>
                </li>
                @endif
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-start gap-4 sm:gap-5">
            <div class="flex-shrink-0">
                <span class="inline-flex bg-orange-500 text-white text-xl sm:text-2xl font-bold px-4 py-2 rounded-xl border border-orange-600 shadow-xs font-mono">
                    Kode {{ $code }}
                </span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-700 bg-orange-100 px-3 py-0.5 rounded-full border border-orange-200">{{ $levelName }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight mb-2">{{ $title }}</h1>
                <p class="text-sm text-slate-600 leading-relaxed max-w-3xl">{{ $description }}</p>
            </div>
        </div>

    </div>
</div>

<!-- Main Content -->
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: List of Children -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="text-lg font-bold text-slate-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Daftar Sub-Klasifikasi (Kode {{ $code }})
                </h2>
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">{{ count($children) }} Turunan</span>
            </div>

            <div class="space-y-3">
                @forelse($children as $child)
                <div class="border border-slate-200/80 rounded-xl overflow-hidden bg-white hover:border-orange-500 hover:shadow-md hover:bg-orange-50/20 transition-all duration-200 group">
                    <a href="{{ route('kbli.show', ['code' => $child['code']]) }}" class="p-4 sm:p-5 flex items-center justify-between transition-colors block">
                        <div class="flex items-center gap-4 min-w-0 pr-3">
                            <span class="min-w-[3.25rem] h-11 px-2.5 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-sm flex-shrink-0 border border-orange-200/80 font-mono shadow-2xs">
                                {{ $child['code'] }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-orange-600 transition-colors leading-snug line-clamp-2">{{ $child['name'] }}</h3>
                                <p class="text-xs text-slate-500 mt-1 font-medium flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 flex-shrink-0"></span>
                                    <span>{{ $child['subtitle'] }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-2">
                            @if($len < 4)
                            <span class="text-xs font-medium text-slate-600 bg-slate-100 px-3 py-1 rounded-full group-hover:bg-orange-100 group-hover:text-orange-700 transition-colors hidden sm:inline-block">
                                {{ $child['total_items'] }} Kelompok
                            </span>
                            @else
                            <span class="text-xs font-bold text-white bg-orange-500 px-3 py-1 rounded-full shadow-2xs hidden sm:inline-block">
                                Detail Usaha
                            </span>
                            @endif
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-orange-500 transform group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </a>
                </div>
                @empty
                <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                    <p class="text-sm text-slate-500">Tidak ada turunan kode KBLI untuk kategori ini.</p>
                </div>
                @endforelse
            </div>

        </div>

        <!-- Right: Meta Data Sidebar -->
        <div class="space-y-6">
            
            <!-- Compact Hierarchy Progress Card -->
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2.5">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Posisi Klasifikasi
                    </h3>
                    <span class="text-[10px] font-semibold px-2 py-0.5 bg-orange-100 text-orange-800 rounded-full font-mono">{{ $len }} Digit</span>
                </div>
                
                <div class="space-y-2">
                    <!-- Level 1: Kategori -->
                    @if($section && $section->category)
                    <a href="{{ route('kbli.show', ['code' => substr($code, 0, 2)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950 transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-slate-400 flex-shrink-0"></span>
                            <span class="font-medium text-slate-900 group-hover:text-orange-950 truncate">{{ $section->category->name }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded ml-1 flex-shrink-0">Kat {{ $section->category->code }}</span>
                    </a>
                    @endif

                    <!-- Level 2: Gol. Pokok -->
                    <a href="{{ route('kbli.show', ['code' => substr($code, 0, 2)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg {{ $len === 2 ? 'bg-orange-50 border border-orange-300 font-bold text-orange-950 shadow-2xs' : 'bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950' }} transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full {{ $len === 2 ? 'bg-orange-500' : 'bg-blue-400' }} flex-shrink-0"></span>
                            <span class="truncate">{{ \App\Helpers\Kbli2025Helper::getName(substr($code, 0, 2)) }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold {{ $len === 2 ? 'bg-orange-500 text-white' : 'bg-blue-100 text-blue-800' }} px-2 py-0.5 rounded ml-1 flex-shrink-0">2 Digit: {{ substr($code, 0, 2) }}</span>
                    </a>

                    <!-- Level 3: Golongan -->
                    @if($len >= 3)
                    <a href="{{ route('kbli.show', ['code' => substr($code, 0, 3)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg {{ $len === 3 ? 'bg-orange-50 border border-orange-300 font-bold text-orange-950 shadow-2xs' : 'bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950' }} transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full {{ $len === 3 ? 'bg-orange-500' : 'bg-indigo-400' }} flex-shrink-0"></span>
                            <span class="truncate">{{ \App\Helpers\Kbli2025Helper::getName(substr($code, 0, 3)) }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold {{ $len === 3 ? 'bg-orange-500 text-white' : 'bg-indigo-100 text-indigo-800' }} px-2 py-0.5 rounded ml-1 flex-shrink-0">3 Digit: {{ substr($code, 0, 3) }}</span>
                    </a>
                    @else
                    <div class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50/60 text-slate-400">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-slate-300 flex-shrink-0"></span>
                            <span class="truncate">Golongan 3 Digit</span>
                        </div>
                        <span class="text-[10px] font-mono bg-slate-200 text-slate-500 px-2 py-0.5 rounded ml-1 flex-shrink-0">3 Digit</span>
                    </div>
                    @endif

                    <!-- Level 4: Subgolongan -->
                    @if($len >= 4)
                    <a href="{{ route('kbli.show', ['code' => substr($code, 0, 4)]) }}" class="flex items-center justify-between text-xs py-2 px-3 rounded-lg {{ $len === 4 ? 'bg-orange-50 border border-orange-300 font-bold text-orange-950 shadow-2xs' : 'bg-slate-50 text-slate-700 hover:bg-orange-100/60 hover:text-orange-950' }} transition-all group block">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full {{ $len === 4 ? 'bg-orange-500' : 'bg-purple-400' }} flex-shrink-0"></span>
                            <span class="truncate">{{ \App\Helpers\Kbli2025Helper::getName(substr($code, 0, 4)) }}</span>
                        </div>
                        <span class="text-[10px] font-mono font-bold {{ $len === 4 ? 'bg-orange-500 text-white' : 'bg-purple-100 text-purple-800' }} px-2 py-0.5 rounded ml-1 flex-shrink-0">4 Digit: {{ substr($code, 0, 4) }}</span>
                    </a>
                    @else
                    <div class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50/60 text-slate-400">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-slate-300 flex-shrink-0"></span>
                            <span class="truncate">Subgolongan 4 Digit</span>
                        </div>
                        <span class="text-[10px] font-mono bg-slate-200 text-slate-500 px-2 py-0.5 rounded ml-1 flex-shrink-0">4 Digit</span>
                    </div>
                    @endif

                    <!-- Level 5: Kelompok -->
                    <div class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-slate-50 text-slate-400 opacity-60">
                        <div class="flex items-center space-x-2 truncate min-w-0 pr-2">
                            <span class="w-2 h-2 rounded-full bg-slate-300 flex-shrink-0"></span>
                            <span class="truncate">Pilih Kelompok 5 Digit</span>
                        </div>
                        <span class="text-[10px] font-mono bg-slate-200 text-slate-600 px-2 py-0.5 rounded ml-1 flex-shrink-0">5 Digit</span>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-slate-600 text-xs leading-relaxed space-y-2">
                <div class="font-bold text-slate-900 text-sm flex items-center mb-1">
                    <svg class="w-4 h-4 mr-1.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Petunjuk Navigasi
                </div>
                <p>Klik pada salah satu kartu turunan di samping untuk menjelajahi rincian klasifikasi lebih dalam hingga ke tingkat paling spesifik (5 Digit Kelompok Usaha).</p>
            </div>

        </div>

    </div>
</div>
@endsection
