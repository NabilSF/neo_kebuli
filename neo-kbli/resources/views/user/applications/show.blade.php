@extends('layouts.app')

@section('title', 'Detail Pengajuan - ' . $data->application->tracking_code)

@section('breadcrumbs')
    <span>/</span>
    <a href="{{ route('user.applications.index') }}" class="hover:text-primary transition-colors">Pengajuan Saya</a>
    <span>/</span>
    <span class="text-slate-900 font-medium">{{ $data->application->tracking_code }}</span>
@endsection

@section('content')
<div class="w-full">
    <!-- Header -->

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-slate-900">{{ $data->application->tracking_code }}</h1>
                    <x-feedback.status-indicator :status="ucfirst(str_replace('_', ' ', $data->application->status->value))" :variant="
                        $data->application->status->value === 'approved' ? 'success' :
                        ($data->application->status->value === 'rejected' ? 'danger' :
                        ($data->application->status->value === 'need_revision' ? 'info' : 'warning'))
                    " />
                </div>
                <p class="text-sm text-slate-500">Diajukan pada {{ $data->application->created_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            
            <div class="flex gap-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-surface border border-border rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm" aria-label="Cetak Detail Pengajuan">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Utama (Kiri) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- 1. Progress Tracker -->
            <div class="bg-surface border border-border rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6">Status Pengajuan</h2>
                <div class="relative flex items-center justify-between w-full">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 rounded"></div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-primary rounded transition-all duration-500" style="width: {{ $data->progress['percentage'] }}%"></div>
                    
                    @foreach($data->progress['stages'] as $stage)
                        <div class="relative flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 {{ $stage['circleClasses'] }} z-10 font-bold text-xs">
                                @if($stage['iconType'] === 'success')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($stage['iconType'] === 'error')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                @elseif($stage['iconType'] === 'warning')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                @else
                                    {{ $stage['iconValue'] }}
                                @endif
                            </div>
                            <span class="mt-2 text-xs font-medium {{ $stage['textClasses'] }}">{{ $stage['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($data->application->status->value === 'need_revision')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 shadow-sm mb-6 flex items-start gap-4">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-amber-800 font-bold text-lg mb-1">Apa yang harus diperbaiki</h3>
                    <p class="text-amber-700 text-sm whitespace-pre-wrap">{{ $data->application->admin_notes ?? 'Silakan periksa kembali data atau dokumen yang dilampirkan.' }}</p>
                </div>
            </div>
            @endif

            @if($data->application->status->value === 'approved')
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6 shadow-sm mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-emerald-800 font-bold text-lg">Pengajuan Disetujui</h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white/60 p-4 rounded-lg border border-emerald-100">
                    <div>
                        <p class="text-xs text-emerald-600/70 font-medium mb-1">Tracking Code</p>
                        <p class="text-sm text-emerald-900 font-bold">{{ $data->application->tracking_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-emerald-600/70 font-medium mb-1">KBLI Disetujui</p>
                        <p class="text-sm text-emerald-900 font-bold">{{ $data->application->kbliItem?->code ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-emerald-600/70 font-medium mb-1">Tanggal Disetujui</p>
                        <p class="text-sm text-emerald-900 font-bold">{{ $data->application->reviewed_at ? $data->application->reviewed_at->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-emerald-600/70 font-medium mb-1">Reviewer</p>
                        <p class="text-sm text-emerald-900 font-bold">{{ $data->application->reviewer?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if($data->application->status->value === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 shadow-sm mb-6 flex items-start gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-red-800 font-bold text-lg mb-1">Pengajuan Ditolak</h3>
                    <p class="text-red-700 text-sm font-medium mb-1">Alasan Penolakan:</p>
                    <p class="text-red-600 text-sm whitespace-pre-wrap">{{ $data->application->admin_notes ?? 'Tidak memenuhi syarat.' }}</p>
                </div>
            </div>
            @endif
            
            <!-- 2. Informasi Produk -->
            <div class="bg-surface border border-border rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">Informasi Produk</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500 mb-1">Nama Produk / Jasa</dt>
                            <dd class="text-base text-slate-900 font-medium">{{ $data->application->product_name }}</dd>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500 mb-1">Kategori KBLI</dt>
                            <dd class="text-base text-slate-900 bg-slate-50 p-3 rounded-lg border border-slate-100">
                                @if($data->application->kbliItem)
                                    <span class="font-bold text-primary">{{ $data->application->kbliItem->code }}</span> - {{ $data->application->kbliItem->name }}
                                @else
                                    <span class="text-slate-400 italic">Belum dipilih</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 mb-1">Bahan Baku</dt>
                            <dd class="text-sm text-slate-900">{{ $data->application->materials ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 mb-1">Proses Produksi</dt>
                            <dd class="text-sm text-slate-900">{{ $data->application->production_process ?? '-' }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500 mb-1">Deskripsi Produk</dt>
                            <dd class="text-sm text-slate-900 whitespace-pre-wrap">{{ $data->application->description ?? '-' }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500 mb-1">Alamat Usaha</dt>
                            <dd class="text-sm text-slate-900">{{ $data->application->address ?? '-' }}</dd>
                        </div>

                        @if($data->application->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500 mb-1">Catatan Tambahan</dt>
                            <dd class="text-sm text-slate-900 bg-amber-50 p-3 rounded-lg border border-amber-100 text-amber-800">{{ $data->application->notes }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- 3. Lampiran -->
            <div class="bg-surface border border-border rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-slate-50/50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Dokumen Lampiran</h2>
                    <span class="text-xs text-slate-500">{{ $data->application->media->count() }} file</span>
                </div>
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">{{ session('error') }}</div>
                    @endif

                    @if($data->application->media->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm text-slate-500">Belum ada dokumen lampiran.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-border rounded-lg border border-border mb-6">
                            @foreach($data->application->media as $media)
                            <li class="flex items-center justify-between py-3 px-4 hover:bg-slate-50/50 transition-colors">
                                <div class="flex items-center min-w-0 gap-3">
                                    <div class="bg-primary/10 p-2 rounded flex-shrink-0 text-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate" title="{{ $media->original_name }}">{{ $media->original_name }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ strtoupper(pathinfo($media->original_name, PATHINFO_EXTENSION)) }}
                                            · {{ number_format($media->size / 1024, 1) }} KB
                                            · {{ $media->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex items-center gap-3">
                                    <a href="{{ route('user.applications.download-media', [$data->application, $media]) }}"
                                       class="text-sm font-medium text-primary hover:text-primary-600 transition-colors"
                                       aria-label="Download {{ $media->original_name }}">
                                        Download
                                    </a>
                                    @if(!in_array($data->application->status, [\App\Enums\WorkflowState::APPROVED, \App\Enums\WorkflowState::REJECTED, \App\Enums\WorkflowState::ARCHIVED]))
                                    <form method="POST"
                                          action="{{ route('user.applications.delete-media', [$data->application, $media]) }}"
                                          onsubmit="return confirm('Hapus dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors"
                                                aria-label="Hapus {{ $media->original_name }}">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Upload Form (only shown if application is still editable) --}}
                    @if(!in_array($data->application->status, [\App\Enums\WorkflowState::APPROVED, \App\Enums\WorkflowState::REJECTED, \App\Enums\WorkflowState::ARCHIVED]))
                    <form method="POST"
                          action="{{ route('user.applications.upload-media', $data->application) }}"
                          enctype="multipart/form-data"
                          x-data="{ fileName: '', dragOver: false }"
                          class="mt-2">
                        @csrf

                        <label class="block cursor-pointer rounded-lg border-2 border-dashed transition-colors p-6 text-center"
                               :class="dragOver ? 'border-primary bg-primary/5' : 'border-slate-300 hover:border-slate-400'"
                               @dragover.prevent="dragOver = true"
                               @dragleave.prevent="dragOver = false"
                               @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0]?.name || ''">
                            <svg class="mx-auto h-10 w-10 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="text-sm text-slate-600 mb-1">
                                <span class="font-semibold text-primary">Pilih file</span> atau seret ke sini
                            </p>
                            <p class="text-xs text-slate-400">PDF, JPG, PNG, DOC, DOCX · Maks 10 MB</p>
                            <input x-ref="fileInput"
                                   type="file"
                                   name="document"
                                   class="sr-only"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   @change="fileName = $refs.fileInput.files[0]?.name || ''"
                                   aria-label="Pilih file dokumen lampiran">
                        </label>

                        <template x-if="fileName">
                            <div class="mt-3 flex items-center justify-between bg-slate-50 rounded-lg px-4 py-2 border border-slate-200">
                                <span class="text-sm text-slate-700 truncate" x-text="fileName"></span>
                                <button type="submit"
                                        class="ml-4 flex-shrink-0 inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Unggah
                                </button>
                            </div>
                        </template>

                        @error('document')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </form>
                    @endif
                </div>
            </div>

            <!-- 5. Approval History -->
            <div class="bg-surface border border-border rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-900">Riwayat Evaluasi (Approval)</h2>
                </div>
                <div class="p-6">
                    @if($data->approvalHistories->isEmpty())
                        <div class="text-center py-6">
                            <p class="text-sm text-slate-500">Belum ada evaluasi dari petugas.</p>
                        </div>
                    @else
                        <div class="relative border-l-2 border-slate-200 ml-3 space-y-6 pb-2">
                            @foreach($data->approvalHistories as $history)
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1.5 w-4 h-4 bg-surface border-2 {{ $history->status->value === 'approved' ? 'border-emerald-500' : ($history->status->value === 'rejected' ? 'border-red-500' : 'border-indigo-500') }} rounded-full"></div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-sm font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $history->status->value)) }}</p>
                                        <span class="text-xs text-slate-500">• {{ $history->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-1">{{ $history->notes }}</p>
                                    @if($history->actor)
                                    <p class="text-xs font-medium text-slate-400">Oleh: {{ $history->actor->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Kolom Samping (Kanan) -->
        <div class="space-y-6">
            
            <!-- 6. Sidebar Ringkas -->
            <div class="bg-surface border border-border rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Informasi Status</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Status Saat Ini</p>
                        <x-feedback.status-indicator :status="ucfirst(str_replace('_', ' ', $data->application->status->value))" :variant="
                            $data->application->status->value === 'approved' ? 'success' :
                            ($data->application->status->value === 'rejected' ? 'danger' :
                            ($data->application->status->value === 'need_revision' ? 'info' : 'warning'))
                        " />
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">Reviewer Bertugas</p>
                        <p class="text-sm font-medium text-slate-900">{{ $data->application->reviewer ? $data->application->reviewer->name : 'Belum Ditugaskan' }}</p>
                    </div>

                    @if($data->application->reviewed_at)
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Tanggal Review</p>
                        <p class="text-sm font-medium text-slate-900">{{ $data->application->reviewed_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-xs text-slate-500 mb-1">Terakhir Diperbarui</p>
                        <p class="text-sm font-medium text-slate-900">{{ $data->application->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- 4. Timeline (ActivityLog) -->
            <div class="bg-surface border border-border rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Log Aktivitas</h3>
                
                @if($data->activityLogs->isEmpty())
                    <p class="text-sm text-slate-500 text-center py-4">Belum ada aktivitas.</p>
                @else
                    <div class="relative border-l border-slate-200 ml-2 space-y-5 pb-1">
                        @foreach($data->activityLogs as $log)
                        <div class="relative pl-5">
                            <div class="absolute -left-[5px] top-1.5 w-2 h-2 bg-slate-300 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $log->action }}</p>
                                @if(isset($log->properties['notes']))
                                    <p class="text-xs text-slate-600 mt-0.5">{{ $log->properties['notes'] }}</p>
                                @endif
                                <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-400">
                                    <span>{{ $log->created_at->format('d M Y, H:i') }}</span>
                                    @if($log->user)
                                        <span>•</span>
                                        <span>{{ $log->user->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
