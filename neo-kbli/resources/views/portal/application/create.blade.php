@extends('layouts.app')

@section('title', 'Pengajuan Klasifikasi Usaha')

@section('breadcrumbs')
    <span>/</span>
    <span class="text-slate-900 dark:text-white font-medium">Ajukan Pengajuan</span>
@endsection

@section('content')
<div class="w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Ajukan Permohonan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftarkan klasifikasi lapangan usaha Anda secara resmi.</p>
    </div>

    @if ($errors->any())
        <div class="mb-8 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 p-5 rounded-lg border border-red-200 dark:border-red-900/50">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <h3 class="text-sm font-semibold">Mohon perbaiki kesalahan berikut:</h3>
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
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-850">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">1. Pilihan KBLI</h2>
            </div>
            
            <div class="p-6" x-data="{
                open: false,
                search: '',
                loading: false,
                selectedId: '{{ old('kbli_item_id', $selectedKbli->id ?? '') }}',
                selectedText: '{{ $selectedKbli ? $selectedKbli->code . ' - ' . $selectedKbli->name : '' }}',
                initialItems: {{ json_encode($kblis->map(fn($k) => ['id' => (string)$k->id, 'code' => $k->code, 'name' => $k->name])) }},
                items: [],
                init() {
                    this.items = [...this.initialItems];
                    if (this.selectedId && !this.selectedText) {
                        const found = this.initialItems.find(i => String(i.id) === String(this.selectedId));
                        if (found) {
                            this.selectedText = found.code + ' - ' + found.name;
                        }
                    }
                },
                get filteredItems() {
                    if (!this.search || this.search.trim() === '') {
                        return this.items;
                    }
                    const q = this.search.toLowerCase();
                    return this.items.filter(i => i.code.toLowerCase().includes(q) || i.name.toLowerCase().includes(q));
                },
                async searchKbli() {
                    const q = this.search.trim();
                    if (q.length < 3) {
                        this.items = [...this.initialItems];
                        return;
                    }
                    this.loading = true;
                    try {
                        let res = await fetch('/api/v1/kbli/search?q=' + encodeURIComponent(q));
                        let json = await res.json();
                        if (json.data && json.data.data && json.data.data.length > 0) {
                            this.items = json.data.data.map(i => ({ id: String(i.id), code: i.code, name: i.name }));
                        }
                    } catch (e) {}
                    this.loading = false;
                },
                selectItem(item) {
                    this.selectedId = item.id;
                    this.selectedText = item.code + ' - ' + item.name;
                    this.search = '';
                    this.open = false;
                },
                clearSelection() {
                    this.selectedId = '';
                    this.selectedText = '';
                    this.search = '';
                    this.open = true;
                    this.items = [...this.initialItems];
                }
            }" @click.outside="open = false">
                
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                    Pilih KBLI Tujuan <span class="text-red-500">*</span>
                </label>

                <!-- Hidden Input for Form Submission -->
                <input type="hidden" name="kbli_item_id" :value="selectedId" required id="kbli_item_id">

                <!-- Tampilan Jika KBLI Sudah Dipilih -->
                <div x-show="selectedId" x-cloak class="mb-2 p-3.5 bg-orange-50 dark:bg-slate-900 border border-orange-200 dark:border-orange-900/60 rounded-lg flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</span>
                        <div class="min-w-0">
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold uppercase tracking-wider">KBLI Terpilih</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="selectedText"></p>
                        </div>
                    </div>
                    <button type="button" @click="clearSelection()" class="px-3 py-1.5 text-xs font-semibold text-orange-600 dark:text-orange-400 hover:bg-orange-100 dark:hover:bg-slate-800 rounded-md border border-orange-300 dark:border-slate-700 transition-colors flex-shrink-0">
                        Ganti KBLI
                    </button>
                </div>

                <!-- Input Pencarian Dropdown -->
                <div class="relative" x-show="!selectedId">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text"
                               x-model="search"
                               @focus="open = true"
                               @input.debounce.300ms="searchKbli()"
                               placeholder="Ketik kode (misal: 10111) atau kata kunci KBLI..."
                               class="w-full pl-10 pr-10 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors">
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Dropdown List Hasil -->
                    <div x-show="open"
                         x-transition.opacity
                         class="absolute z-30 mt-1.5 w-full bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-gray-200 dark:border-slate-700 max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-slate-800">
                        
                        <div x-show="loading" class="p-3 text-center text-xs text-slate-500 dark:text-slate-400">
                            Mencari data KBLI...
                        </div>

                        <template x-for="item in filteredItems" :key="item.id">
                            <button type="button"
                                    @click="selectItem(item)"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-orange-50 dark:hover:bg-slate-800/80 transition-colors flex items-start gap-2.5 group">
                                <span class="px-2 py-0.5 rounded font-mono text-xs font-bold bg-orange-100 dark:bg-orange-950/70 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800/60 flex-shrink-0 mt-0.5" x-text="item.code"></span>
                                <span class="text-slate-800 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 leading-snug" x-text="item.name"></span>
                            </button>
                        </template>

                        <div x-show="!loading && filteredItems.length === 0" class="p-4 text-center text-xs text-slate-500 dark:text-slate-400">
                            Tidak ditemukan KBLI dengan kata kunci tersebut.
                        </div>
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                    Cari kode atau kata kunci KBLI usaha Anda di atas untuk memilih klasifikasi yang sesuai.
                </p>
            </div>
        </div>

        <!-- Section 2: Informasi Produk -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-850">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">2. Informasi Produk</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="product_name" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="product_name"
                           id="product_name"
                           value="{{ old('product_name') }}"
                           required
                           placeholder="Contoh: Keripik Tempe Aneka Rasa"
                           class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors">
                </div>

                <div>
                    <label for="product_material" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">
                        Terbuat dari Apa <span class="text-red-500">*</span>
                    </label>
                    <textarea name="product_material"
                              id="product_material"
                              rows="3"
                              required
                              placeholder="Jelaskan bahan baku utama yang digunakan..."
                              class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors">{{ old('product_material') }}</textarea>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-slate-400">Jelaskan bahan baku utama yang digunakan untuk membuat produk.</p>
                </div>

                <div>
                    <label for="production_process" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">
                        Bagaimana Proses Produksinya <span class="text-red-500">*</span>
                    </label>
                    <textarea name="production_process"
                              id="production_process"
                              rows="4"
                              required
                              placeholder="Jelaskan tahapan proses pembuatan dari bahan mentah hingga siap dijual..."
                              class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors">{{ old('production_process') }}</textarea>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-slate-400">Jelaskan tahapan utama proses produksi hingga menghasilkan produk akhir.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Lokasi -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-850">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">3. Lokasi & Keterangan</h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="saker_address" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">
                        Kode Satker <span class="text-red-500">*</span>
                    </label>
                    <select name="saker_address"
                            id="saker_address"
                            required
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors">
                        <option value="" class="dark:bg-slate-900 text-slate-400">Pilih Kode Satker</option>
                        @foreach($satkers as $code => $name)
                            <option value="{{ $code }}" {{ old('saker_address') == $code ? 'selected' : '' }} class="dark:bg-slate-900 text-slate-900 dark:text-white">
                                {{ $code }} - {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-slate-400">Pilih kode Satker lokasi kegiatan usaha atau proses produksi berlangsung.</p>
                </div>

                <div>
                    <label for="product_description" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">
                        Deskripsi Produk <span class="text-gray-400 dark:text-slate-500 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="product_description"
                              id="product_description"
                              rows="3"
                              placeholder="Informasi tambahan lain jika ada..."
                              class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors">{{ old('product_description') }}</textarea>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-slate-400">Tambahkan informasi lain yang dapat membantu proses klasifikasi KBLI.</p>
                </div>
            </div>
        </div>

        <!-- Section 4: Document Upload -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden shadow-xs">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-850">
                <h2 class="text-base font-bold text-gray-900 dark:text-white">4. Unggah Dokumen</h2>
            </div>
            <div class="p-6" x-data="{ fileName: '' }">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                    Dokumen Pendukung <span class="text-xs font-normal text-gray-500 dark:text-slate-400">(PDF/JPG/PNG, Maks 5MB)</span>
                </label>
                
                <div class="mt-2 flex justify-center px-6 pt-6 pb-6 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl relative hover:border-orange-500 dark:hover:border-orange-500 hover:bg-orange-50/20 dark:hover:bg-slate-900/40 transition-colors group">
                    <div class="space-y-2 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-slate-500 group-hover:text-orange-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <!-- Tampilan Jika File Belum Dipilih -->
                        <div x-show="!fileName" class="flex text-sm text-slate-600 dark:text-slate-300 justify-center items-center gap-1" translate="no">
                            <label for="document" class="relative cursor-pointer font-bold text-orange-600 dark:text-orange-400 hover:text-orange-500 hover:underline">
                                <span>Unggah file</span>
                                <input id="document"
                                       name="document"
                                       type="file"
                                       class="sr-only"
                                       required
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            </label>
                            <span>atau tarik dan lepas di sini</span>
                        </div>

                        <!-- Tampilan Jika File Sudah Dipilih -->
                        <div x-show="fileName" x-cloak class="flex flex-col items-center gap-1">
                            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span x-text="fileName"></span>
                            </p>
                            <label for="document" class="text-xs text-orange-600 dark:text-orange-400 hover:underline cursor-pointer">
                                Ganti file
                            </label>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-slate-400">
                            Format yang didukung: PDF, PNG, JPG hingga 5MB
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="pt-6 border-t border-gray-200 dark:border-slate-800 flex justify-start">
            <button type="submit" class="w-full sm:w-auto px-10 py-3.5 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                <span>Kirim Permohonan</span>
            </button>
        </div>

    </form>
</div>
@endsection
