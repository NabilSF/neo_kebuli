<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header 
            title="Daftar Pengajuan" 
            description="Manajemen seluruh transaksi pengajuan KBLI dari pengguna."
        />
    </x-slot>

    <x-data-display.table :empty="$applications->isEmpty()">
        
        <x-slot name="toolbar">
            <x-workspace.toolbar>
                <x-slot name="searchRegion">
                    <form action="{{ route('admin.applications.index') }}" method="GET" class="w-full sm:max-w-xs">
                        <x-forms.input name="search" type="search" placeholder="Cari kode tracking..." value="{{ request('search') }}" />
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                    </form>
                </x-slot>
                <x-slot name="filters">
                    <form action="{{ route('admin.applications.index') }}" method="GET" class="flex items-center gap-2" id="filter-form">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="status" class="text-sm border-border rounded-md   py-2" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </form>
                </x-slot>
            </x-workspace.toolbar>
            
            @if(request('search') || request('status'))
                <div class="px-4 py-2 border-t border-border bg-slate-50 flex items-center gap-2">
                    <span class="text-sm text-slate-500">Filter aktif:</span>
                    @if(request('search'))
                        <x-data-display.filter-chips label="Pencarian" dismissible="false">{{ request('search') }}</x-data-display.filter-chips>
                    @endif
                    @if(request('status'))
                        <x-data-display.filter-chips label="Status" dismissible="false">{{ \App\Enums\WorkflowState::tryFrom(request('status'))?->label() }}</x-data-display.filter-chips>
                    @endif
                    <a href="{{ route('admin.applications.index') }}" class="text-sm text-primary hover:underline ml-2">Hapus Filter</a>
                </div>
            @endif
        </x-slot>

        <x-slot name="head">
            <x-data-display.head>Kode Tracking</x-data-display.head>
            <x-data-display.head>Produk</x-data-display.head>
            <x-data-display.head>Pemohon</x-data-display.head>
            <x-data-display.head>Tanggal</x-data-display.head>
            <x-data-display.head>Status</x-data-display.head>
            <x-data-display.head action>Aksi</x-data-display.head>
        </x-slot>

        @foreach($applications as $app)
            <x-data-display.row>
                <x-data-display.cell class="font-medium text-primary">{{ $app->tracking_code }}</x-data-display.cell>
                <x-data-display.cell>
                    <div class="max-w-xs truncate" title="{{ $app->product_name }}">{{ $app->product_name }}</div>
                </x-data-display.cell>
                <x-data-display.cell>{{ $app->user->name ?? '-' }}</x-data-display.cell>
                <x-data-display.cell>{{ $app->submitted_at ? $app->submitted_at->translatedFormat('d M Y') : '-' }}</x-data-display.cell>
                <x-data-display.cell>
                    <x-feedback.status-indicator 
                        :status="$app->status->label()" 
                        :variant="$app->status->badgeVariant()" 
                    />
                </x-data-display.cell>
                <x-data-display.cell action>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.applications.show', $app) }}" class="text-primary hover:text-primary-dark">Detail & Tinjau</a>
                    </div>
                </x-data-display.cell>
            </x-data-display.row>
        @endforeach

        <x-slot name="pagination">
            <x-data-display.pagination :paginator="$applications" />
        </x-slot>

    </x-data-display.table>

</x-layouts.admin>
