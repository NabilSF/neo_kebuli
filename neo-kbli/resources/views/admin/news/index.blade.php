@extends('layouts.admin')

@section('title', 'Berita & Pengumuman')

@section('content')
<div class="bg-white rounded-md border border-gray-200 p-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Berita & Pengumuman</h3>
            <p class="text-sm text-gray-500 mt-0.5">Kelola artikel, regulasi, dan pengumuman resmi.</p>
        </div>
        <a href="{{ route('admin.news.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 border border-primary-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Berita
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-700">
        {{ session('error') }}
    </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto border border-gray-200 rounded-md">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Kategori</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Penulis</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($news as $article)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-900 line-clamp-1">{{ $article->title }}</div>
                        @if($article->excerpt)
                        <div class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $article->excerpt }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-600">
                        {{ $article->category->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($article->status === 'published')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">Diterbitkan</span>
                        @elseif($article->status === 'draft')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">Draf</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">Diarsipkan</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-500 text-xs">
                        {{ $article->created_at->format('d M Y') }}
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-600">
                        {{ $article->author->name ?? '—' }}
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.news.edit', $article->id) }}"
                                class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Ubah
                            </a>
                            @if($article->status !== 'published')
                            <form action="{{ route('admin.news.update', $article->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="title" value="{{ $article->title }}">
                                <input type="hidden" name="category_id" value="{{ $article->news_category_id }}">
                                <input type="hidden" name="content" value="{{ $article->content }}">
                                <input type="hidden" name="status" value="published">
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1 border border-green-300 rounded-md text-xs font-medium text-green-700 bg-white hover:bg-green-50">
                                    Terbitkan
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.news.update', $article->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="title" value="{{ $article->title }}">
                                <input type="hidden" name="category_id" value="{{ $article->news_category_id }}">
                                <input type="hidden" name="content" value="{{ $article->content }}">
                                <input type="hidden" name="status" value="draft">
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1 border border-yellow-300 rounded-md text-xs font-medium text-yellow-700 bg-white hover:bg-yellow-50">
                                    Tarik
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.news.destroy', $article->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Hapus berita ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1 border border-red-200 rounded-md text-xs font-medium text-red-600 bg-white hover:bg-red-50">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Belum ada berita yang dibuat.</p>
                        <a href="{{ route('admin.news.create') }}" class="mt-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Tambah Berita Pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $news->withQueryString()->links() }}
    </div>
</div>
@endsection
