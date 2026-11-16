<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header 
            title="Detail Pengajuan {{ $application->tracking_code }}" 
            description="Tinjau informasi permohonan, dokumen pendukung, dan riwayat status."
        >
            <x-slot name="status">
                <x-feedback.status-indicator 
                    :status="$application->status->label()" 
                    :variant="$application->status->badgeVariant()" 
                />
            </x-slot>
            <x-slot name="actions">
                <x-ui.button variant="outline" onclick="window.location='{{ route('admin.applications.index') }}'">
                    Kembali
                </x-ui.button>
            </x-slot>
        </x-workspace.header>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Content Column -->
        <div class="lg:col-span-2 space-y-8">
            <x-workspace.summary-panel title="Informasi Pemohon">
                <x-workspace.summary-item label="Nama Lengkap" :value="$application->user->name ?? '-'" />
                <x-workspace.summary-item label="Email" :value="$application->user->email ?? '-'" />
                @php
                    $satkerName = config('satker.' . $application->saker_address);
                    $satkerDisplay = $satkerName ? $application->saker_address . ' — ' . $satkerName : ($application->saker_address ?? '-');
                @endphp
                <x-workspace.summary-item label="Kode Satker" :value="$satkerDisplay" />
            </x-workspace.summary-panel>

            <x-workspace.summary-panel title="Detail Produk & KBLI">
                <x-workspace.summary-item label="Nama Produk" :value="$application->product_name ?? '-'" />
                <x-workspace.summary-item label="Kode KBLI" :value="$application->kbliItem->code ?? '-'" />
                <x-workspace.summary-item label="Nama KBLI" :value="$application->kbliItem->name ?? '-'" />
                <x-workspace.summary-item label="Proses Produksi">
                    <div class="prose prose-sm max-w-none text-slate-700">
                        {{ $application->production_process ?: 'Tidak ada deskripsi tersedia.' }}
                    </div>
                </x-workspace.summary-item>
            </x-workspace.summary-panel>

            @if($application->status === \App\Enums\WorkflowState::SUBMITTED || $application->status === \App\Enums\WorkflowState::UNDER_REVIEW)
                @can('assign', $application)
                <x-ui.card>
                    <h3 class="text-base font-semibold leading-6 text-slate-900 mb-4">Penugasan Reviewer</h3>
                    
                    @if($application->reviewer)
                        <div class="mb-4 p-4 bg-slate-50 rounded-md flex justify-between items-center border border-border">
                            <div>
                                <p class="text-sm font-medium text-slate-900">Ditugaskan kepada:</p>
                                <p class="text-sm text-slate-500">{{ $application->reviewer->name }}</p>
                            </div>
                            <x-feedback.status-indicator status="Aktif" variant="success" />
                        </div>
                    @endif

                    <form action="{{ route('admin.applications.assign', $application) }}" method="POST" class="flex flex-col gap-4">
                        @csrf
                        <x-forms.field label="Pilih Peninjau (Reviewer)" name="reviewer_id">
                            <select name="reviewer_id" class="w-full rounded-md border-border   text-sm h-10">
                                <option value="">Pilih pengguna...</option>
                                @foreach($reviewers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </x-forms.field>
                        
                        <x-forms.field label="Catatan Penugasan (Opsional)" name="notes">
                            <x-forms.input type="text" name="notes" placeholder="Catatan internal..." />
                        </x-forms.field>

                        <div class="flex justify-end pt-2 border-t border-border mt-2">
                            <x-ui.button type="submit" variant="primary" size="sm">Tugaskan Pengajuan</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
                @endcan
            @endif

            @if($application->status === \App\Enums\WorkflowState::UNDER_REVIEW)
                @can('approve', $application)
                    {{-- Keputusan Peninjauan: layout vertikal agar tidak terpotong --}}
                    <x-ui.card>
                        {{-- Header --}}
                        <div class="mb-5 pb-4 border-b border-border">
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Keputusan Peninjauan</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tentukan status akhir pengajuan ini berdasarkan hasil evaluasi.</p>
                        </div>

                        {{-- Tombol Tolak & Minta Revisi --}}
                        <div class="flex flex-wrap items-center gap-3 mb-5">
                            <form action="{{ route('admin.applications.approve', $application) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak permohonan ini?');">
                                @csrf
                                <input type="hidden" name="decision" value="rejected">
                                <button type="submit" class="inline-flex items-center justify-center font-medium transition-colors rounded-md px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tolak
                                </button>
                            </form>

                            <form action="{{ route('admin.applications.approve', $application) }}" method="POST" onsubmit="return confirm('Yakin ingin meminta revisi pada permohonan ini?');">
                                @csrf
                                <input type="hidden" name="decision" value="need_revision">
                                <button type="submit" class="inline-flex items-center justify-center font-medium transition-colors rounded-md px-4 py-2 text-sm bg-amber-500 hover:bg-amber-600 text-white">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Minta Revisi
                                </button>
                            </form>
                        </div>

                        {{-- Form Setujui & Publikasikan --}}
                        <div class="rounded-lg border border-green-200 dark:border-green-900/40 bg-green-50/50 dark:bg-green-950/20 p-5">
                            <h4 class="text-sm font-semibold text-green-800 dark:text-green-300 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Finalisasi KBLI Baru
                            </h4>

                            <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="space-y-4" onsubmit="return confirm('Setujui permohonan dan publikasikan KBLI baru?');">
                                @csrf
                                <input type="hidden" name="decision" value="approved">

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Kode KBLI <span class="text-xs text-slate-400">(5 Digit)</span></label>
                                    <input
                                        type="text"
                                        name="code"
                                        pattern="\d{5}"
                                        maxlength="5"
                                        title="Harus 5 digit angka"
                                        placeholder="Contoh: 12345"
                                        required
                                        class="block w-full rounded-md border border-border bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Kategori / Sektor KBLI</label>
                                    <select
                                        name="section_id"
                                        required
                                        class="block w-full rounded-md border border-border bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                                    >
                                        <option value="">Pilih Sektor KBLI...</option>
                                        @foreach(\App\Models\KbliSection::all() as $section)
                                            <option value="{{ $section->id }}">{{ $section->code }} - {{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="w-full inline-flex items-center justify-center font-semibold transition-colors rounded-md px-4 py-2.5 text-sm bg-green-600 hover:bg-green-700 active:bg-green-800 text-white">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Setujui &amp; Publikasikan
                                </button>
                            </form>
                        </div>
                    </x-ui.card>
                @endcan
            @endif

            <!-- Komentar Internal Umum -->
            <x-ui.card>
                <h3 class="text-base font-semibold leading-6 text-slate-900 mb-4">Tambahkan Catatan Internal</h3>
                <x-workspace.comment-box 
                    action="{{ route('admin.applications.add_memo', $application) }}"
                    method="POST"
                    placeholder="Masukkan catatan atau memo yang tidak akan mengubah status..."
                    buttonText="Simpan Catatan"
                >
                </x-workspace.comment-box>
            </x-ui.card>

        </div>

        <!-- Sidebar Column -->
        <div class="space-y-8">
            <!-- Timeline Riwayat -->
            <x-ui.card>
                <h3 class="text-sm font-medium text-slate-900 mb-6">Riwayat Aktivitas</h3>
                
                @if($application->activityLogs->isEmpty())
                    <x-feedback.empty-state 
                        title="Belum ada aktivitas" 
                        description="Pengajuan ini belum memiliki jejak rekam perubahan."
                    />
                @else
                    <x-data-display.timeline>
                        @foreach($application->activityLogs()->latest()->get() as $log)
                            <x-data-display.timeline-item 
                                :isLast="$loop->last"
                                :date="$log->created_at->translatedFormat('d M Y')"
                                :time="$log->created_at->format('H:i')"
                                :user="$log->user->name ?? 'Sistem'"
                                :action="$log->action"
                                :variant="$log->new_status ? (\App\Enums\WorkflowState::tryFrom($log->new_status)?->badgeVariant() ?? 'gray') : 'info'"
                            >
                                @if($log->notes)
                                    <div class="p-3 bg-slate-50 border border-border rounded-md text-slate-600 mt-2 text-xs italic">
                                        "{{ $log->notes }}"
                                    </div>
                                @endif
                            </x-data-display.timeline-item>
                        @endforeach
                    </x-data-display.timeline>
                @endif
            </x-ui.card>
        </div>

    </div>
</x-layouts.admin>
