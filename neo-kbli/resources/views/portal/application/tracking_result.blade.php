@extends('layouts.guest')

@section('title', "Pelacakan: {$application->tracking_code}")

@section('content')
<div class="bg-gray-50 dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('tracking.index') }}" class="inline-flex items-center text-sm font-semibold text-orange-600 dark:text-orange-400 hover:text-orange-500 mb-6 transition-colors">
            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Lacak Permohonan Lain
        </a>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Status Permohonan</h1>
                <p class="text-gray-500 dark:text-slate-400 font-mono text-lg font-bold">{{ $application->tracking_code }}</p>
            </div>
            
            <div class="flex-shrink-0">
                <x-application.status-badge :status="$application->status" />
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <!-- Left Col: Details -->
    <div class="md:col-span-2 space-y-6">
        
        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 p-5 rounded-xl mb-6 flex items-start gap-3">
                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="font-bold">Permohonan Diterima!</h3>
                    <p class="text-sm mt-1">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 shadow-xs">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-slate-700 pb-3">Informasi Produk & Pengajuan</h2>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Target KBLI</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium flex items-center gap-2">
                        <span class="bg-orange-100 dark:bg-orange-950/70 text-orange-600 dark:text-orange-400 px-2.5 py-1 rounded-md border border-orange-200 dark:border-orange-800/60 font-mono font-bold">{{ $application->kbliItem->code ?? 'N/A' }}</span>
                        <span>{{ $application->kbliItem->name ?? 'N/A' }}</span>
                    </dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Nama Produk / Usaha</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $application->product_name ?? 'N/A' }}</dd>
                </div>
                
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Kode Satker</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        @php
                            $satkerName = config('satker.' . $application->saker_address);
                            echo $satkerName ? $application->saker_address . ' — ' . $satkerName : ($application->saker_address ?? 'N/A');
                        @endphp
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Bahan Produk</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $application->product_material ?? 'Tidak tersedia (Pengajuan Lama)' }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Proses Produksi</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $application->production_process ?? 'Tidak tersedia (Pengajuan Lama)' }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-slate-400">Deskripsi Produk</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $application->product_description ?? '-' }}</dd>
                </div>
            </dl>
        </div>

    </div>

    <div class="md:col-span-1">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 sticky top-24 shadow-xs">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-slate-700 pb-3">Status Timeline</h2>
            
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($application->histories as $index => $history)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-slate-700" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full {{ $loop->first ? 'bg-orange-500 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-400 dark:text-slate-500' }} flex items-center justify-center font-bold">
                                        @if($loop->first)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <div class="w-2.5 h-2.5 bg-gray-400 dark:bg-slate-500 rounded-full"></div>
                                        @endif
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        @php
                                            $tlLabels = [
                                                'draft' => 'Draf',
                                                'submitted' => 'Diajukan',
                                                'under_review' => 'Dalam Peninjauan',
                                                'need_revision' => 'Perlu Revisi',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                            ];
                                            $tlLabel = $tlLabels[$history->to_status] ?? str_replace('_', ' ', $history->to_status);
                                        @endphp
                                        <p class="text-sm text-gray-500 dark:text-slate-400">Diubah menjadi <span class="font-bold text-gray-900 dark:text-white uppercase">{{ $tlLabel }}</span></p>
                                        @if($history->notes)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-slate-300 bg-gray-50 dark:bg-slate-900/60 p-2 rounded-lg border border-gray-100 dark:border-slate-700">{{ $history->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right text-xs whitespace-nowrap text-gray-400 dark:text-slate-500">
                                        <time datetime="{{ $history->created_at }}">{{ $history->created_at->format('d M Y') }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

</div>
@endsection
