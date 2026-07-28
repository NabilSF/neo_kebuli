@extends('layouts.app')

@section('title', 'Pengajuan Klasifikasi Usaha')

@section('content')
<div class="bg-gray-50 border-b border-gray-200 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Ajukan Permohonan</h1>
        <p class="text-gray-600">Daftarkan klasifikasi lapangan usaha Anda secara resmi.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow">

    @if ($errors->any())
        <div class="mb-8 bg-red-50 text-red-700 p-5 rounded-md border border-red-100">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <h3 class="text-sm font-medium">Mohon perbaiki kesalahan berikut:</h3>
                    <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('application.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Section 1: KBLI Selection -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">1. Pilihan KBLI</h2>
            </div>
            <div class="p-6">
                <label for="kbli_item_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih KBLI Tujuan</label>
                <select name="kbli_item_id" id="kbli_item_id" required class="block w-full px-4 py-3 rounded-md border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 bg-white">
                    <option value="">-- Pilih KBLI --</option>
                    @foreach($kblis as $kbli)
                        <option value="{{ $kbli->id }}" {{ (old('kbli_item_id') == $kbli->id || ($selectedKbli && $selectedKbli->id == $kbli->id)) ? 'selected' : '' }}>
                            {{ $kbli->code }} - {{ Str::limit($kbli->name, 80) }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-sm text-gray-500">Pilih klasifikasi baku lapangan usaha untuk kegiatan bisnis Anda.</p>
            </div>
        </div>

        <!-- Section 2: Informasi Produk -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">2. Informasi Produk</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" required class="block w-full px-4 py-3 rounded-md border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>

                <div>
                    <label for="product_material" class="block text-sm font-medium text-gray-700 mb-1">Terbuat dari Apa <span class="text-red-500">*</span></label>
                    <textarea name="product_material" id="product_material" rows="3" required class="block w-full px-4 py-3 rounded-md border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('product_material') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Jelaskan bahan baku utama yang digunakan untuk membuat produk.</p>
                </div>

                <div>
                    <label for="production_process" class="block text-sm font-medium text-gray-700 mb-1">Bagaimana Proses Produksinya <span class="text-red-500">*</span></label>
                    <textarea name="production_process" id="production_process" rows="4" required class="block w-full px-4 py-3 rounded-md border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('production_process') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Jelaskan tahapan utama proses produksi hingga menghasilkan produk akhir.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Lokasi -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">3. Lokasi & Keterangan</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="saker_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat SAKER <span class="text-red-500">*</span></label>
                    <textarea name="saker_address" id="saker_address" rows="3" required class="block w-full px-4 py-3 rounded-md border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('saker_address') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Masukkan alamat lokasi kegiatan usaha atau proses produksi berlangsung.</p>
                </div>

                <div>
                    <label for="product_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Produk <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <textarea name="product_description" id="product_description" rows="3" class="block w-full px-4 py-3 rounded-md border border-gray-300 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">{{ old('product_description') }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Tambahkan informasi lain yang dapat membantu proses klasifikasi KBLI.</p>
                </div>
            </div>
        </div>

        <!-- Section 4: Document Upload -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">4. Unggah Dokumen</h2>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-4">Dokumen Pendukung (PDF/JPG/PNG, Maks 5MB)</label>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border border-gray-300 rounded-md relative hover:border-primary-400 hover:bg-primary-50 transition-colors group">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-primary-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none px-1">
                                <span>Unggah file</span>
                                <input id="document" name="document" type="file" class="sr-only" required accept=".pdf,.jpg,.jpeg,.png">
                            </label>
                            <p class="pl-1">atau tarik dan lepas</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            PDF, PNG, JPG hingga 5MB
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-200">
            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-md border border-primary-600 transition-colors">
                Kirim Permohonan
            </button>
        </div>

    </form>

</div>
@endsection
