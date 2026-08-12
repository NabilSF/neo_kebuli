@extends('layouts.app')

@section('title', 'Pengajuan Saya - Neo KBLI')

@section('breadcrumbs')
    <span>/</span>
    <span class="text-slate-900 font-medium">Pengajuan Saya</span>
@endsection

@section('content')
<div class="w-full" x-data="{
    search: '{{ $params['search'] ?? '' }}',
    status: '{{ $params['status'] ?? 'all' }}',
    updateQuery() {
        const url = new URL(window.location.href);
        if (this.search) {
            url.searchParams.set('search', this.search);
        } else {
            url.searchParams.delete('search');
        }
        
        if (this.status !== 'all') {
            url.searchParams.set('status', this.status);
        } else {
            url.searchParams.delete('status');
        }
        
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    }
}">
    <!-- 1. Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Pengajuan Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola seluruh pengajuan KBLI Anda.</p>
    </div>

    <!-- 2. Ringkasan Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <x-ui.card class="p-4 flex flex-col justify-center border-l-4 border-l-slate-400">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-900">{{ $data->totalApplications }}</p>
        </x-ui.card>
        <x-ui.card class="p-4 flex flex-col justify-center border-l-4 border-l-amber-400">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Menunggu</p>
            <p class="text-2xl font-bold text-amber-500">{{ $data->pendingReview }}</p>
        </x-ui.card>
        <x-ui.card class="p-4 flex flex-col justify-center border-l-4 border-l-indigo-400">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Revisi</p>
            <p class="text-2xl font-bold text-indigo-500">{{ $data->needRevision }}</p>
        </x-ui.card>
        <x-ui.card class="p-4 flex flex-col justify-center border-l-4 border-l-emerald-400">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Disetujui</p>
            <p class="text-2xl font-bold text-emerald-500">{{ $data->approved }}</p>
        </x-ui.card>
        <x-ui.card class="p-4 flex flex-col justify-center border-l-4 border-l-red-400">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Ditolak</p>
            <p class="text-2xl font-bold text-red-500">{{ $data->rejected }}</p>
        </x-ui.card>
    </div>

    <!-- 4. Filter & 5. Search -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 hide-scrollbar" role="group" aria-label="Filter status pengajuan">
            <button type="button" @click="status = 'all'; updateQuery()" :class="{'bg-primary text-white border-primary': status === 'all', 'bg-surface text-slate-600 border-border hover:bg-slate-50': status !== 'all'}" class="px-4 py-2 rounded-full text-sm font-medium border transition-colors whitespace-nowrap">Semua</button>
            <button type="button" @click="status = 'pending'; updateQuery()" :class="{'bg-amber-500 text-white border-amber-500': status === 'pending', 'bg-surface text-slate-600 border-border hover:bg-slate-50': status !== 'pending'}" class="px-4 py-2 rounded-full text-sm font-medium border transition-colors whitespace-nowrap">Menunggu</button>
            <button type="button" @click="status = 'revision'; updateQuery()" :class="{'bg-indigo-500 text-white border-indigo-500': status === 'revision', 'bg-surface text-slate-600 border-border hover:bg-slate-50': status !== 'revision'}" class="px-4 py-2 rounded-full text-sm font-medium border transition-colors whitespace-nowrap">Revisi</button>
            <button type="button" @click="status = 'approved'; updateQuery()" :class="{'bg-emerald-500 text-white border-emerald-500': status === 'approved', 'bg-surface text-slate-600 border-border hover:bg-slate-50': status !== 'approved'}" class="px-4 py-2 rounded-full text-sm font-medium border transition-colors whitespace-nowrap">Disetujui</button>
            <button type="button" @click="status = 'rejected'; updateQuery()" :class="{'bg-red-500 text-white border-red-500': status === 'rejected', 'bg-surface text-slate-600 border-border hover:bg-slate-50': status !== 'rejected'}" class="px-4 py-2 rounded-full text-sm font-medium border transition-colors whitespace-nowrap">Ditolak</button>
        </div>

        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input x-model="search" @input.debounce.500ms="updateQuery()" type="search" aria-label="Cari tracking code atau nama produk" placeholder="Cari tracking code..." autocomplete="off" class="block w-full pl-10 pr-3 py-2 border border-border rounded-lg leading-5 bg-surface placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-colors">
        </div>
    </div>

    <!-- 7. Empty State -->
    @if($data->applications->isEmpty())
        <div class="bg-surface rounded-xl border border-border p-12 text-center shadow-sm">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h2 class="text-xl font-semibold text-slate-900 mb-2">Belum ada pengajuan.</h2>
            <p class="text-slate-500 max-w-md mx-auto mb-8">Tidak ada data KBLI yang cocok dengan kriteria pencarian Anda.</p>
            <a href="{{ route('application.create') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-primary hover:bg-primary-600 text-white font-medium transition-colors">
                Ajukan Pengajuan
            </a>
        </div>
    @else
        <!-- 3. Tabel Pengajuan (Desktop) & 10. Mobile Card List -->
        
        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block bg-surface rounded-xl border border-border overflow-hidden mb-6">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tracking Code</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Produk / KBLI</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Terakhir Diperbarui</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-border">
                    @foreach($data->applications as $app)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                            {{ $app->tracking_code }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900 line-clamp-1" title="{{ $app->product_name }}">{{ $app->product_name }}</div>
                            <div class="text-sm text-slate-500 line-clamp-1" title="{{ $app->kbliItem?->code }} - {{ $app->kbliItem?->name }}">{{ $app->kbliItem?->code ?? '-' }} - {{ $app->kbliItem?->name ?? 'Belum Ditentukan' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <!-- 6. Status Badge -->
                            <x-feedback.status-indicator :status="ucfirst(str_replace('_', ' ', $app->status->value))" :variant="
                                $app->status->value === 'approved' ? 'success' :
                                ($app->status->value === 'rejected' ? 'danger' :
                                ($app->status->value === 'need_revision' ? 'info' : 'warning'))
                            " />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $app->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $app->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <!-- 8. Action -->
                            <a href="{{ route('tracking.show', ['code' => $app->tracking_code]) }}" class="text-primary hover:text-primary-600 font-medium inline-flex items-center gap-1">
                                Lihat Detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List (Hidden on Desktop) -->
        <div class="md:hidden space-y-4 mb-6">
            @foreach($data->applications as $app)
            <div class="bg-surface rounded-xl border border-border p-4 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-xs text-slate-500 font-medium">{{ $app->created_at->format('d M Y') }}</p>
                        <p class="font-bold text-slate-900 text-lg">{{ $app->tracking_code }}</p>
                    </div>
                    <x-feedback.status-indicator :status="ucfirst(str_replace('_', ' ', $app->status->value))" :variant="
                        $app->status->value === 'approved' ? 'success' :
                        ($app->status->value === 'rejected' ? 'danger' :
                        ($app->status->value === 'need_revision' ? 'info' : 'warning'))
                    " />
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-slate-900">{{ $app->product_name }}</p>
                    <p class="text-sm text-slate-500">{{ $app->kbliItem?->code ?? '-' }} - {{ $app->kbliItem?->name ?? 'Belum Ditentukan' }}</p>
                </div>

                <div class="flex justify-between items-center border-t border-border pt-3 mt-2">
                    <p class="text-xs text-slate-400">Diperbarui {{ $app->updated_at->diffForHumans() }}</p>
                    <a href="{{ route('tracking.show', ['code' => $app->tracking_code]) }}" class="text-sm text-primary hover:text-primary-600 font-medium inline-flex items-center gap-1">
                        Detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- 9. Pagination -->
        <div class="mt-6">
            {{ $data->applications->links() }}
        </div>
    @endif
</div>
@endsection
