<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header 
            title="Katalog KBLI" 
            description="Manajemen seluruh master data Klasifikasi Baku Lapangan Usaha Indonesia."
        >
            <x-slot name="actions">
                <x-ui.button variant="outline" onclick="window.location='{{ route('admin.kbli.import.form') }}'">
                    Import Data
                </x-ui.button>
                <x-ui.button variant="primary" onclick="window.location='{{ route('admin.kbli.items.create') }}'">
                    Tambah KBLI
                </x-ui.button>
            </x-slot>
        </x-workspace.header>
    </x-slot>

    <x-data-display.table :empty="$items->isEmpty()">
        
        <x-slot name="toolbar">
            <x-workspace.toolbar>
                <x-slot name="searchRegion">
                    <form action="{{ route('admin.kbli.items.index') }}" method="GET" class="w-full sm:max-w-xs">
                        <x-forms.input name="search" type="search" placeholder="Cari kode atau nama..." value="{{ request('search') }}" />
                        @if(request('section_id'))
                            <input type="hidden" name="section_id" value="{{ request('section_id') }}">
                        @endif
                    </form>
                </x-slot>
                <x-slot name="filters">
                    <form action="{{ route('admin.kbli.items.index') }}" method="GET" class="flex items-center gap-2" id="filter-form">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="category_id" class="text-sm border-border rounded-md py-2" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select name="section_id" class="text-sm border-border rounded-md py-2" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Semua Golongan Pokok</option>
                            @foreach($sections as $id => $name)
                                <option value="{{ $id }}" {{ request('section_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select name="is_active" class="text-sm border-border rounded-md py-2" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </form>
                </x-slot>
            </x-workspace.toolbar>
            
            @if(request('search') || request('section_id'))
                <div class="px-4 py-2 border-t border-border bg-slate-50 flex items-center gap-2">
                    <span class="text-sm text-slate-500">Filter aktif:</span>
                    @if(request('search'))
                        <x-data-display.filter-chips label="Pencarian" dismissible="false">{{ request('search') }}</x-data-display.filter-chips>
                    @endif
                    @if(request('section_id'))
                        <x-data-display.filter-chips label="Golongan" dismissible="false">{{ $sections[request('section_id')] ?? request('section_id') }}</x-data-display.filter-chips>
                    @endif
                    <a href="{{ route('admin.kbli.items.index') }}" class="text-sm text-primary hover:underline ml-2">Hapus Filter</a>
                </div>
            @endif
            
            <div id="bulk-action-bar" class="hidden px-4 py-2 border-t border-border bg-blue-50 flex items-center justify-between">
                <span class="text-sm font-medium text-blue-700">Terpilih <span id="selection-count">0</span> KBLI</span>
                <form action="{{ route('admin.kbli.items.bulk-update') }}" method="POST" id="bulk-action-form" class="flex items-center gap-2" onsubmit="return confirm('Lanjutkan aksi massal?');">
                    @csrf
                    <div id="hidden-bulk-ids"></div>
                    <select name="bulk_action" class="text-sm border-border rounded-md py-1 px-2 h-9" required>
                        <option value="">-- Pilih Aksi Massal --</option>
                        <option value="activate">Aktifkan Terpilih</option>
                        <option value="deactivate">Nonaktifkan Terpilih</option>
                    </select>
                    <x-ui.button type="submit" variant="primary">Terapkan</x-ui.button>
                </form>
            </div>
        </x-slot>

        <x-slot name="head">
            <x-data-display.head>
                <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" title="Pilih Semua">
            </x-data-display.head>
            <x-data-display.head sortable direction="{{ $sortBy === 'code' ? $direction : '' }}" onclick="window.location='{{ request()->fullUrlWithQuery(['sort' => 'code', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}'">Kode</x-data-display.head>
            <x-data-display.head sortable direction="{{ $sortBy === 'name' ? $direction : '' }}" onclick="window.location='{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}'">Nama Lapangan Usaha</x-data-display.head>
            <x-data-display.head>Kategori & Golongan</x-data-display.head>
            <x-data-display.head>Status</x-data-display.head>
            <x-data-display.head action>Aksi</x-data-display.head>
        </x-slot>

        @foreach($items as $item)
            <x-data-display.row>
                <x-data-display.cell>
                    <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ $item->id }}">
                </x-data-display.cell>
                <x-data-display.cell class="font-medium">{{ $item->code }}</x-data-display.cell>
                <x-data-display.cell>
                    <div class="max-w-md truncate" title="{{ $item->name }}">{{ $item->name }}</div>
                </x-data-display.cell>
                <x-data-display.cell>
                    <div class="text-xs text-gray-500">{{ $item->section->category->name ?? '-' }}</div>
                    <div>{{ $item->section->name ?? '-' }}</div>
                </x-data-display.cell>
                <x-data-display.cell>
                    <x-feedback.status-indicator :status="$item->is_active ? 'aktif' : 'nonaktif'" />
                </x-data-display.cell>
                <x-data-display.cell action>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.kbli.items.show', $item) }}" class="text-primary hover:text-primary-dark">Detail</a>
                        <a href="{{ route('admin.kbli.items.edit', $item) }}" class="text-slate-500 hover:text-primary">Ubah</a>
                        
                        <form action="{{ route('admin.kbli.items.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger hover:text-red-700">Hapus</button>
                        </form>
                    </div>
                </x-data-display.cell>
            </x-data-display.row>
        @endforeach

        <x-slot name="pagination">
            <div class="px-4 py-3 border-t border-border flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Menampilkan total <span class="font-medium text-slate-700">{{ $items->total() }}</span> KBLI
                </div>
                <div>
                    <x-data-display.pagination :paginator="$items" />
                </div>
            </div>
        </x-slot>

    </x-data-display.table>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('select-all-checkbox');
            const checkboxes = document.querySelectorAll('.bulk-checkbox');
            const bulkBar = document.getElementById('bulk-action-bar');
            const countDisplay = document.getElementById('selection-count');
            const hiddenInputsContainer = document.getElementById('hidden-bulk-ids');
            
            function updateBulkUI() {
                const checked = document.querySelectorAll('.bulk-checkbox:checked');
                const count = checked.length;
                countDisplay.textContent = count;
                
                if (count > 0) {
                    bulkBar.classList.remove('hidden');
                } else {
                    bulkBar.classList.add('hidden');
                }
                
                hiddenInputsContainer.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'bulk_ids[]';
                    input.value = cb.value;
                    hiddenInputsContainer.appendChild(input);
                });
                
                selectAll.checked = count > 0 && count === checkboxes.length;
            }
            
            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkUI();
                });
            }
            
            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkUI);
            });
        });
    </script>
</x-layouts.admin>
