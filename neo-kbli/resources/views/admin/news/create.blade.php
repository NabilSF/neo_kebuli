@extends('layouts.admin')

@section('title', 'Tambah Berita')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.news.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-primary-600">
        <svg class="mr-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Berita
    </a>
</div>

<div class="bg-white rounded-md border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Tambah Berita Baru</h3>

    @if($errors->any())
    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-sm text-red-700">
        <ul class="list-disc pl-4 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Berita <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm    "
                    placeholder="Masukkan judul berita...">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" id="category_id" required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm     bg-white">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Publikasi <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm     bg-white">
                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draf</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Diterbitkan</option>
                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Ringkasan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="excerpt" id="excerpt" rows="2"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm    "
                    placeholder="Ringkasan singkat artikel untuk ditampilkan di halaman daftar berita...">{{ old('excerpt') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label for="penulis" class="block text-sm font-medium text-gray-700 mb-1">Penulis <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text" name="penulis" id="penulis" value="{{ old('penulis') }}"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm    "
                    placeholder="Kosongkan untuk otomatis menggunakan nilai: Badan Pusat Statistik (BPS)">
            </div>

            <div class="md:col-span-2">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Isi Artikel <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="16" required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono    "
                    placeholder="Tulis isi artikel di sini...">{{ old('content') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">HTML dasar diperbolehkan (p, strong, em, ul, ol, li, h2, h3).</p>
            </div>

            <div class="md:col-span-2">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail <span class="text-gray-400 font-normal">(opsional, maks. 2MB)</span></label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                    class="block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:text-sm file:font-medium file:text-gray-700 file:bg-white hover:file:bg-gray-50">
                <div id="thumbnail-preview" class="mt-3 hidden">
                    <img id="thumbnail-img" src="" alt="Pratinjau thumbnail" class="h-40 w-auto rounded-md border border-gray-200 object-cover">
                </div>
                <script>
                    document.getElementById('thumbnail').addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                document.getElementById('thumbnail-img').src = ev.target.result;
                                document.getElementById('thumbnail-preview').classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 border border-primary-600">
                Simpan Berita
            </button>
            <a href="{{ route('admin.news.index') }}"
                class="inline-flex items-center px-5 py-2 bg-white text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 border border-gray-300">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
