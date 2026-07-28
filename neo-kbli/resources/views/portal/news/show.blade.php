@extends('layouts.app')

@section('title', $article->title . ' - Neo KBLI')
@section('meta_description', $article->excerpt ?? Str::limit(strip_tags($article->content), 150))

@section('content')

{{-- Breadcrumb --}}
<div class="bg-white border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="text-xs text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
            <span class="mx-1.5">›</span>
            <a href="{{ route('news.index') }}" class="hover:text-gray-600">Berita & Pengumuman</a>
            <span class="mx-1.5">›</span>
            <span class="text-gray-600 line-clamp-1">{{ Str::limit($article->title, 60) }}</span>
        </nav>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white border border-gray-200 rounded-md overflow-hidden">

        {{-- Article Header --}}
        <div class="px-6 py-6 border-b border-gray-100">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <time class="text-xs text-gray-400" datetime="{{ $article->created_at }}">{{ $article->created_at->format('d F Y') }}</time>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-snug">{{ $article->title }}</h1>
            <div class="flex items-center gap-2 mt-4 text-sm text-gray-500">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>{{ $article->penulis ?: 'Badan Pusat Statistik (BPS)' }}</span>
            </div>
        </div>

        {{-- Thumbnail --}}
        @if($article->thumbnail)
        <div class="border-b border-gray-100">
            <img src="{{ Storage::url($article->thumbnail) }}"
                alt="{{ $article->title }}"
                class="w-full max-h-96 object-cover">
        </div>
        @endif

        {{-- Article Content --}}
        <div class="px-6 py-8">
            <div class="prose prose-sm max-w-none text-gray-800 leading-relaxed">
                {!! $article->content !!}
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <a href="{{ route('news.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-primary-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Berita
            </a>
            <button onclick="navigator.share ? navigator.share({title: '{{ addslashes($article->title) }}', url: window.location.href}) : (navigator.clipboard.writeText(window.location.href), alert('Tautan disalin!'))"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 border border-gray-300 rounded-md px-3 py-1.5 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Bagikan
            </button>
        </div>
    </div>

    {{-- Related / Other Articles --}}
    @if($others->count() > 0)
    <div class="mt-10">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Artikel Lainnya</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach($others as $other)
            <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
                @if($other->thumbnail)
                    <img src="{{ Storage::url($other->thumbnail) }}" alt="{{ $other->title }}" class="w-full h-28 object-cover border-b border-gray-100">
                @else
                    <div class="w-full h-28 bg-gray-50 border-b border-gray-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                @endif
                <div class="p-4">
                    <p class="text-xs text-gray-400 mb-1">{{ $other->created_at->format('d M Y') }}</p>
                    <a href="{{ route('news.show', $other->slug) }}" class="text-sm font-semibold text-gray-800 hover:text-primary-700 line-clamp-2 leading-snug">{{ $other->title }}</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection
