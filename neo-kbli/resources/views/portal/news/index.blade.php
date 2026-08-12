@extends('layouts.guest')

@section('title', 'Berita & Pengumuman - Neo KBLI')
@section('meta_description', 'Informasi terbaru mengenai KBLI, regulasi, pembaruan sistem, dan pengumuman resmi dari Neo KBLI.')

@section('content')

{{-- Hero Kecil --}}
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="text-xs text-gray-400 mb-2">
            <a href="{{ route('home') }}" class="hover:text-gray-600">Beranda</a>
            <span class="mx-1.5">›</span>
            <span class="text-gray-600">Berita & Pengumuman</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Berita & Pengumuman</h1>
        <p class="text-sm text-gray-500 mt-1">Informasi terbaru mengenai KBLI, regulasi, pembaruan sistem, dan pengumuman resmi.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">
            @if($news->count() > 0)

                <div class="space-y-4">
                    @foreach($news as $article)
                    <article class="bg-white border border-gray-200 rounded-md overflow-hidden flex flex-col sm:flex-row hover:bg-gray-50 transition-colors">
                        {{-- Thumbnail (Left) --}}
                        <div class="sm:w-48 sm:shrink-0">
                            @if($article->thumbnail)
                                <img src="{{ Storage::url($article->thumbnail) }}"
                                    alt="{{ $article->title }}"
                                    loading="lazy"
                                    class="w-full h-48 sm:h-full object-cover border-b sm:border-b-0 sm:border-r border-gray-100">
                            @else
                                <div class="w-full h-48 sm:h-full bg-gray-50 border-b sm:border-b-0 sm:border-r border-gray-100 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Content (Right) --}}
                        <div class="p-4 sm:p-5 flex flex-col flex-grow justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <time class="text-xs text-gray-400" datetime="{{ $article->created_at }}">{{ $article->created_at->format('d M Y') }}</time>
                                    <span class="text-gray-300">·</span>
                                    <span class="text-xs text-gray-400">{{ $article->penulis ?: 'Badan Pusat Statistik (BPS)' }}</span>
                                </div>

                                <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-1 leading-snug">
                                    <a href="{{ route('news.show', $article->slug) }}" class="hover:text-primary-700">{{ $article->title }}</a>
                                </h2>

                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                    {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 120) }}
                                </p>
                            </div>

                            <div class="mt-2">
                                <a href="{{ route('news.show', $article->slug) }}"
                                    class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800">
                                    Baca Selengkapnya
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $news->links() }}
                </div>

            @else
                {{-- Compact Empty State --}}
                <div class="bg-gray-50 border border-gray-200 rounded-md p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">Belum ada berita.</h3>
                    <p class="text-sm text-gray-600 mb-4">Administrator belum mempublikasikan berita apapun.</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Kembali ke Beranda
                    </a>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="w-full lg:w-72 shrink-0 space-y-5">

            {{-- Pencarian Berita --}}
            <div class="bg-white border border-gray-200 rounded-md p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b border-gray-100 pb-2">Cari Berita</h3>
                <form action="{{ route('news.index') }}" method="GET">
                    <div class="flex gap-2">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm   "
                            placeholder="Kata kunci...">
                        <button type="submit" class="px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 border border-primary-600">Cari</button>
                    </div>
                </form>
            </div>

            {{-- Berita Terbaru --}}
            <div class="bg-white border border-gray-200 rounded-md p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b border-gray-100 pb-2">Berita Terbaru</h3>
                @forelse($latestNews as $latest)
                <div class="py-2 border-b border-gray-50 last:border-0">
                    <a href="{{ route('news.show', $latest->slug) }}" class="text-sm text-gray-700 hover:text-primary-700 font-medium line-clamp-2 leading-snug">{{ $latest->title }}</a>
                    <p class="text-xs text-gray-400 mt-1">{{ $latest->created_at->format('d M Y') }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-500 italic">Belum ada berita.</p>
                @endforelse
            </div>

            {{-- Kategori Berita --}}
            <div class="bg-white border border-gray-200 rounded-md p-4 sm:p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b border-gray-100 pb-2">Kategori Berita</h3>
                @forelse($newsCategories as $cat)
                <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                    <a href="{{ route('news.index', ['category' => $cat->slug]) }}" class="text-sm text-gray-700 hover:text-primary-700">{{ $cat->name }}</a>
                    <span class="text-xs text-gray-500 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-md">{{ $cat->news_count }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 italic">Belum ada kategori.</p>
                @endforelse
            </div>

        </aside>
    </div>
</div>

@endsection
