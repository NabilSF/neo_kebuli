@extends('layouts.admin')

@section('title', 'Detail Permohonan - ' . $application->tracking_code)

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.applications.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">Detail Permohonan</h2>
            <x-application.status-badge :status="$application->status" />
        </div>
        <p class="text-gray-500 mt-1 ml-9">Kode Pelacakan: <span class="font-mono font-medium text-gray-900">{{ $application->tracking_code }}</span></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column (Main Data) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Target KBLI Box -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Target KBLI</h3>
            </div>
            <div class="p-6">
                @if($application->kbliItem)
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-primary-100 text-primary-700 font-bold text-lg">
                                {{ $application->kbliItem->code }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">{{ $application->kbliItem->name }}</h4>
                            <p class="mt-1 text-sm text-gray-500 line-clamp-3">{{ $application->kbliItem->description }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Target KBLI tidak ditentukan atau telah dihapus.</p>
                @endif
            </div>
        </div>

        <!-- Product Details Box -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Detail Produk / Usaha</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Produk / Usaha</h4>
                    <p class="text-base text-gray-900 font-medium">{{ $application->product_name ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Bahan Produk</h4>
                    <p class="text-base text-gray-900">{{ $application->product_material ?? 'Tidak tersedia (Pengajuan Lama)' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Proses Produksi</h4>
                    <p class="text-base text-gray-900 whitespace-pre-wrap">{{ $application->production_process ?? 'Tidak tersedia (Pengajuan Lama)' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Alamat SAKER / Usaha</h4>
                    <p class="text-base text-gray-900">{{ $application->saker_address ?? 'Tidak tersedia' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi Produk</h4>
                    <p class="text-base text-gray-900 whitespace-pre-wrap">{{ $application->product_description ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Documents Box -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Dokumen Pendukung</h3>
            </div>
            <div class="p-6">
                @if($application->media->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach($application->media as $doc)
                            <li class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">{{ $doc->original_name }}</span>
                                </div>
                                <a href="{{ route('admin.documents.download', $doc->id) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    Unduh
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 italic">Tidak ada dokumen pendukung.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column (Sidebar) -->
    <div class="space-y-6">
        
        @php
            use App\Enums\ApplicationStatus;
            $currentStatus = $application->status instanceof ApplicationStatus ? $application->status : ApplicationStatus::from($application->status);
        @endphp

        <!-- Review Decision -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Keputusan Tinjauan</h3>
            </div>
            <div class="p-6">
                @if($currentStatus->isTerminal())
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-lg border border-blue-100 flex items-start gap-3">
                        <svg class="w-6 h-6 flex-shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold">Permohonan ini telah mencapai status akhir.</p>
                            @if($application->reviewer)
                            <p class="text-xs mt-1 opacity-80">Diselesaikan oleh {{ $application->reviewer->name }} pada {{ $application->reviewed_at?->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <form action="{{ route('admin.applications.update_status', $application->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Perbarui Status</label>
                            <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                                <option value="">Pilih keputusan...</option>
                                @foreach($currentStatus->allowedTransitions() as $transition)
                                    <option value="{{ $transition->value }}" {{ old('status') === $transition->value ? 'selected' : '' }}>
                                        {{ __('status.' . $transition->value) ?? ucwords(str_replace('_', ' ', $transition->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Tinjauan</label>
                            <textarea name="notes" id="notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Masukkan catatan di sini...">{{ old('notes') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Catatan wajib diisi saat menolak atau meminta revisi.</p>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            Simpan Keputusan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Reviewer Information -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Penugasan Peninjau</h3>
            </div>
            <div class="p-6">
                @if($application->reviewer)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                            {{ substr($application->reviewer->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $application->reviewer->name }}</p>
                            <p class="text-xs text-gray-500">Terakhir Ditinjau: {{ $application->reviewed_at?->format('d M Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Belum ada peninjau.</p>
                @endif
            </div>
        </div>

        <!-- Application History -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Jejak Audit</h3>
            </div>
            <div class="p-6">
                <x-application.history-timeline :histories="$application->histories" />
            </div>
        </div>
        
    </div>
</div>
@endsection
