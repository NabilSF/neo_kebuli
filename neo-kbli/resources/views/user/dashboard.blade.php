@extends('layouts.app')

@section('title', 'Dashboard Pemohon - NEO KEBULI')

@section('breadcrumbs')
    <!-- Dashboard is root, no extra segments needed -->
@endsection

@section('content')
<div class="w-full">
    <!-- 1. Greeting -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    @if($dashboardData->totalApplications === 0)
        <!-- 6. Empty State -->
        <div class="bg-surface rounded-xl border border-border p-12 text-center shadow-sm">
            <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h2 class="text-xl font-semibold text-slate-900 mb-2">Belum ada permohonan</h2>
            <p class="text-slate-500 max-w-md mx-auto mb-8">Anda belum memiliki riwayat pengajuan KBLI. Mulai pengajuan pertama Anda sekarang untuk mendaftarkan usaha Anda.</p>
            <a href="{{ route('application.create') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-primary hover:bg-primary-600 text-white font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ajukan Permohonan Pertama
            </a>
        </div>
    @else
        <!-- 2. Statistik Pengajuan & 4. Quick Action -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <x-ui.card class="p-5 flex flex-col justify-center">
                    <p class="text-sm font-medium text-muted mb-1">Total</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $dashboardData->totalApplications }}</p>
                </x-ui.card>
                <x-ui.card class="p-5 flex flex-col justify-center">
                    <p class="text-sm font-medium text-muted mb-1">Menunggu</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $dashboardData->pendingReview }}</p>
                </x-ui.card>
                <x-ui.card class="p-5 flex flex-col justify-center">
                    <p class="text-sm font-medium text-muted mb-1">Revisi</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $dashboardData->needRevision }}</p>
                </x-ui.card>
                <x-ui.card class="p-5 flex flex-col justify-center">
                    <p class="text-sm font-medium text-muted mb-1">Selesai</p>
                    <div class="flex items-end gap-2">
                        <p class="text-3xl font-bold text-slate-900">{{ $dashboardData->approved }}</p>
                        @if($dashboardData->rejected > 0)
                            <span class="text-sm text-danger font-medium mb-1 border-l border-border pl-2">{{ $dashboardData->rejected }} Ditolak</span>
                        @endif
                    </div>
                </x-ui.card>
            </div>
            <div class="flex flex-col gap-3">
                <a href="{{ route('application.create') }}" class="w-full h-full min-h-[3rem] flex items-center justify-center px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded font-medium transition-colors border border-accent shadow-sm focus:ring-2 focus:ring-accent focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajukan KBLI Baru
                </a>
                <div class="grid grid-cols-2 gap-3 h-full min-h-[3rem]">
                    <a href="{{ route('tracking.index') }}" class="flex items-center justify-center px-4 py-2 bg-surface hover:bg-secondary text-primary rounded border border-border font-medium transition-colors text-sm">Lacak</a>
                    <a href="{{ route('user.applications.index') }}" class="flex items-center justify-center px-4 py-2 bg-surface hover:bg-secondary text-primary rounded border border-border font-medium transition-colors text-sm">Semua Data</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- 3. Pengajuan Terbaru -->
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Pengajuan Terbaru</h2>
                </div>
                <div class="bg-surface rounded-xl border border-border overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-border">
                                    <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Produk / KBLI</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Terakhir Diperbarui</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($dashboardData->recentApplications as $app)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 px-4 text-sm font-medium text-slate-900">
                                        {{ $app->tracking_code }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-sm font-medium text-slate-900 truncate max-w-[200px]">{{ $app->product_name }}</p>
                                        <p class="text-xs text-slate-500 truncate max-w-[200px]">{{ $app->kbliItem?->code ?? '-' }} - {{ $app->kbliItem?->name ?? 'Belum Ditentukan' }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-slate-500">
                                        {{ $app->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <x-feedback.status-indicator :status="ucfirst(str_replace('_', ' ', $app->status->value))" :variant="
                                            $app->status->value === 'approved' ? 'success' :
                                            ($app->status->value === 'rejected' ? 'danger' :
                                            ($app->status->value === 'need_revision' ? 'info' : 'warning'))
                                        " />
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('user.applications.show', $app->id) }}" class="inline-flex items-center text-sm font-medium text-primary hover:text-primary-600 transition-colors">
                                            Detail <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. Timeline Aktivitas -->
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-4">Aktivitas Terakhir</h2>
                <div class="bg-surface rounded-xl border border-border p-5">
                    @if($dashboardData->recentActivities->isEmpty())
                        <p class="text-sm text-slate-500 text-center py-4">Belum ada aktivitas.</p>
                    @else
                        <div class="relative border-l-2 border-slate-200 ml-3 space-y-6 pb-2">
                            @foreach($dashboardData->recentActivities as $activity)
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1.5 w-4 h-4 bg-surface border-2 border-primary rounded-full"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $activity->action }}</p>
                                    @if(isset($activity->properties['notes']))
                                        <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $activity->properties['notes'] }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
