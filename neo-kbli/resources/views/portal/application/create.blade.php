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
                search: '',
                allKblis: {{ json_encode($kblis->map(fn($k) => ['id' => (string)$k->id, 'code' => $k->code, 'name' => $k->name])) }},
                selectedId: '{{ old('kbli_item_id', $selectedKbli->id ?? '') }}',
                selectedText: '{{ $selectedKbli ? $selectedKbli->code . ' - ' . $selectedKbli->name : '' }}',
                init() {
                    if (this.selectedId && !this.selectedText) {
                        const found = this.allKblis.find(i => String(i.id) === String(this.selectedId));
                        if (found) {
                            this.selectedText = found.code + ' - ' + found.name;
                        }
                    }
                },
                filterOptions() {
                    const q = this.search.toLowerCase().trim();
                    const select = this.$refs.kbliSelect;
                    const currentVal = this.selectedId;
                    
                    select.innerHTML = '';
                    
                    const filtered = !q 
                        ? this.allKblis 
                        : this.allKblis.filter(k => k.code.toLowerCase().includes(q) || k.name.toLowerCase().includes(q));
                    
                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = '';
                    defaultOpt.textContent = q 
                        ? (filtered.length > 0 ? `-- Ditemukan ${filtered.length} KBLI (Silakan Pilih) --` : '-- Tidak ada KBLI yang cocok --')
                        : '-- Pilih KBLI Tujuan ({{ $kblis->count() }} Tersedia) --';
                    select.appendChild(defaultOpt);
                    
                    filtered.forEach(k => {
                        const opt = document.createElement('option');
                        opt.value = k.id;
                        opt.textContent = `${k.code} - ${k.name}`;
                        if (String(k.id) === String(currentVal)) {
                            opt.selected = true;
                        }
                        select.appendChild(opt);
                    });
                },
                onSelectChange(e) {
                    this.selectedId = e.target.value;
                    const found = this.allKblis.find(i => String(i.id) === String(this.selectedId));
                    this.selectedText = found ? found.code + ' - ' + found.name : '';
                },
                clearSearch() {
                    this.search = '';
                    this.filterOptions();
                }
            }">
                
                <label for="kbli_item_id" class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                    Pilih KBLI Tujuan <span class="text-red-500">*</span>
                </label>

                <!-- 1. Kotak Filter / Pencarian Cepat -->
                <div class="mb-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text"
                               x-model="search"
                               @input.debounce.150ms="filterOptions()"
                               placeholder="Ketik kode (misal: 1011 atau 10111) atau nama KBLI untuk menyaring pilihan..."
                               class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors">
                        <button type="button" 
                                x-show="search" 
                                @click="clearSearch()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- 2. Dropdown <select> Asli -->
                <div class="mb-3">
                    <select name="kbli_item_id"
                            id="kbli_item_id"
                            required
                            x-ref="kbliSelect"
                            @change="onSelectChange($event)"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:border-orange-500 dark:focus:border-orange-500 transition-colors cursor-pointer">
                        <option value="">-- Pilih KBLI Tujuan ({{ $kblis->count() }} Tersedia) --</option>
                        @foreach($kblis as $kbli)
                            <option value="{{ $kbli->id }}" {{ old('kbli_item_id', $selectedKbli->id ?? '') == $kbli->id ? 'selected' : '' }}>
                                {{ $kbli->code }} - {{ $kbli->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Tampilan KBLI Terpilih -->
                <div x-show="selectedId && selectedText" class="p-3.5 bg-orange-50 dark:bg-slate-900/80 border border-orange-200 dark:border-orange-900/60 rounded-lg flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</span>
                        <div class="min-w-0">
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold uppercase tracking-wider">KBLI Terpilih</p>
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="selectedText"></p>
                        </div>
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">
                    Gunakan kotak pencarian di atas untuk memfilter daftar pilihan KBLI di dalam dropdown secara instan.
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
