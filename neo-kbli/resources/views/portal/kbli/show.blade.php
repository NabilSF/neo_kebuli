@extends('layouts.app')

@section('title', "KBLI {$item->code} - {$item->name}")
@section('meta_description', Str::limit(strip_tags($item->description), 150))

@section('content')
<!-- Header & Breadcrumbs -->
<div class="bg-gray-50 border-b border-gray-200 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 flex-wrap">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Beranda
                    </a>
                </li>
                @if($item->section && $item->section->category)
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('home') }}#explore" class="ml-1 md:ml-2 hover:text-primary-600 transition-colors">Kat {{ $item->section->category->code }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('home') }}#explore" class="ml-1 md:ml-2 hover:text-primary-600 transition-colors">Bag {{ $item->section->code }}</a>
                    </div>
                </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="ml-1 md:ml-2 text-gray-800 font-semibold">{{ $item->code }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-start gap-4">
            <div class="flex-shrink-0">
                <span class="inline-flex bg-primary-600 text-white text-2xl font-bold px-3 py-1.5 rounded-md border border-primary-700">
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
                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Deskripsi
                </h2>
                <div class="prose prose-lg text-gray-700 max-w-none">
                    {!! nl2br(e($item->description)) !!}
                </div>
            </section>

        </div>

        <!-- Right: Meta Data Sidebar -->
        <div class="space-y-6">
            
            <!-- Hierarchy Card -->
            @if($item->section && $item->section->category)
            <div class="bg-white border border-gray-200 rounded-md p-5">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Hierarki Klasifikasi</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Kategori {{ $item->section->category->code }}</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $item->section->category->name }}</p>
                    </div>
                    <div class="pl-4 border-l-2 border-primary-200">
                        <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Bagian {{ $item->section->code }}</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $item->section->name }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Call to Action -->
            <div class="bg-gray-50 border border-gray-200 rounded-md p-6 text-center relative overflow-hidden mt-8">
                <div class="relative z-10">
                    <h3 class="font-bold text-lg mb-2 text-gray-900">Siap Mengajukan?</h3>
                    <p class="text-sm text-gray-600 mb-4">Mulai proses pendaftaran bisnis Anda menggunakan kode KBLI ini.</p>
                    <a href="{{ route('application.create', ['kbli' => $item->code]) }}" class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-md border border-primary-600 transition-colors">
                        Ajukan Sekarang
                    </a>
                </div>
                <!-- Decorative BG -->
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-primary-500 opacity-20 rounded-full blur-2xl"></div>
            </div>

        </div>

    </div>
</div>
@endsection
