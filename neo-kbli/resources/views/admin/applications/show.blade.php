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
                    <x-workspace.approval-card 
                        title="Keputusan Peninjauan" 
                        description="Tentukan status akhir pengajuan ini berdasarkan hasil evaluasi."
                    >
                        <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="inline-block" onsubmit="return confirm('Tolak permohonan?');">
                            @csrf
                            <input type="hidden" name="decision" value="rejected">
                            <x-ui.button type="submit" variant="danger">Tolak</x-ui.button>
                        </form>
                        
                        <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="inline-block" onsubmit="return confirm('Minta revisi?');">
                            @csrf
                            <input type="hidden" name="decision" value="need_revision">
                            <x-ui.button type="submit" variant="warning">Minta Revisi</x-ui.button>
                        </form>

                        <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="mt-4 border-t border-border pt-4" onsubmit="return confirm('Setujui permohonan dan publikasikan KBLI baru?');">
                            @csrf
                            <input type="hidden" name="decision" value="approved">
                            
                            <div class="mb-4 space-y-4 text-left">
                                <h4 class="text-sm font-semibold text-slate-900">Finalisasi KBLI Baru</h4>
                                
                                <x-forms.field label="Kode KBLI (5 Digit)" name="code">
                                    <x-forms.input type="text" name="code" pattern="\d{5}" title="Harus 5 digit angka" placeholder="Contoh: 12345" required />
                                </x-forms.field>

                                <x-forms.field label="Kategori/Sektor" name="section_id">
                                    <select name="section_id" required class="w-full rounded-md border-border text-sm h-10">
                                        <option value="">Pilih Sektor KBLI...</option>
                                        @foreach(\App\Models\KbliSection::all() as $section)
                                            <option value="{{ $section->id }}">{{ $section->code }} - {{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </x-forms.field>
                            </div>

                            <x-ui.button type="submit" variant="success" class="w-full justify-center">Setujui & Publikasikan</x-ui.button>
                        </form>
                    </x-workspace.approval-card>
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
