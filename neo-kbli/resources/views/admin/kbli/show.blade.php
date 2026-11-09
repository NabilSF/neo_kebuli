<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header 
            title="Detail KBLI" 
            description="Menampilkan informasi rinci dari data Klasifikasi Baku Lapangan Usaha Indonesia."
        >
            <x-slot name="status">
                <x-feedback.status-indicator :status="$item->is_active ? 'aktif' : 'nonaktif'" />
            </x-slot>
            <x-slot name="actions">
                <x-ui.button variant="outline" onclick="window.location='{{ route('admin.kbli.items.edit', $item) }}'">
                    Ubah Data
                </x-ui.button>
                <x-ui.button variant="outline" onclick="window.location='{{ route('admin.kbli.items.index') }}'">
                    Kembali
                </x-ui.button>
            </x-slot>
        </x-workspace.header>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            <x-workspace.summary-panel title="Informasi Utama">
                <x-workspace.summary-item label="Kode KBLI" :value="$item->code" />
                <x-workspace.summary-item label="Nama Lapangan Usaha" :value="$item->name" />
                <x-workspace.summary-item label="Deskripsi Umum">
                    <div class="prose prose-sm max-w-none text-slate-700">
                        {{ $item->description ?: 'Tidak ada deskripsi tersedia.' }}
                    </div>
                </x-workspace.summary-item>
            </x-workspace.summary-panel>

            <x-workspace.summary-panel title="Hierarki Klasifikasi KBLI (5 Level)">
                <x-workspace.summary-item label="1. Kategori" :value="($item->section->category->code ?? '-') . ' - ' . ($item->section->category->name ?? '-')" />
                <x-workspace.summary-item label="2. Golongan Pokok (2 Digit)" :value="substr($item->code, 0, 2) . ' - ' . ($item->section->name ?? 'Golongan Pokok ' . substr($item->code, 0, 2))" />
                <x-workspace.summary-item label="3. Golongan (3 Digit)" :value="substr($item->code, 0, 3) . ' (Merinci golongan pokok berdasarkan jenis aktivitas)'" />
                <x-workspace.summary-item label="4. Subgolongan (4 Digit)" :value="substr($item->code, 0, 4) . ' (Merinci lagi kegiatan dalam suatu golongan)'" />
                <x-workspace.summary-item label="5. Kelompok (5 Digit)" :value="$item->code . ' - ' . $item->name . ' (Jenis usaha spesifik)'" />
            </x-workspace.summary-panel>
        </div>

        <div class="space-y-8">
            <x-workspace.statistic-card 
                title="Total Pengajuan (Simulasi)" 
                value="142" 
                trend="up" 
                trendValue="12% bulan ini" 
            />

            <x-ui.card>
                <h3 class="text-sm font-medium text-slate-900 mb-4">Metadata Sistem</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Dibuat pada</dt>
                        <dd class="text-slate-900">{{ $item->created_at->translatedFormat('d F Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Terakhir diubah</dt>
                        <dd class="text-slate-900">{{ $item->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </x-ui.card>
        </div>

    </div>

</x-layouts.admin>
