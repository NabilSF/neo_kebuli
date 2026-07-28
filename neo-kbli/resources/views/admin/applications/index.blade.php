@extends('layouts.admin')

@section('title', 'Antrian Tinjauan Permohonan')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Antrian Tinjauan Permohonan</h2>
        <p class="text-gray-500 mt-1">Kelola dan tinjau pengajuan KBLI dari pemohon.</p>
    </div>
</div>

<!-- Filter Box -->
<div class="bg-white rounded-md border border-gray-200 p-6 mb-6">
    <form action="{{ route('admin.applications.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="sr-only">Cari</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari berdasarkan kode pelacakan, produk, atau pemohon..." class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
        </div>
        <div class="w-full md:w-64">
            <label for="status" class="sr-only">Status</label>
            <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">
                <option value="">Semua Status</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Diajukan</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Dalam Peninjauan</option>
                <option value="need_revision" {{ request('status') === 'need_revision' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-md hover:bg-primary-700 border border-primary-600">
            Saring
        </button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.applications.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-white text-gray-700 font-medium rounded-md hover:bg-gray-50 border border-gray-300">
                Hapus Filter
            </a>
        @endif
    </form>
</div>

<!-- Data Table -->
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    @if($applications->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="px-6 py-4">Kode Pelacakan</th>
                        <th class="px-6 py-4">Target KBLI</th>
                        <th class="px-6 py-4">Produk / Usaha</th>
                        <th class="px-6 py-4">Pemohon</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($applications as $application)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm font-medium text-gray-900">{{ $application->tracking_code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $application->kbliItem->code ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900 font-medium line-clamp-1">
                                {{ $application->product_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">
                                {{ $application->applicant->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <x-application.status-badge :status="$application->status" size="sm" />
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $application->submitted_at->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.applications.show', $application->id) }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                                Tinjau &rarr;
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $applications->withQueryString()->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak Ada Permohonan</h3>
            <p class="text-gray-500">Saat ini tidak ada pengajuan yang sesuai dengan kriteria Anda.</p>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.applications.index') }}" class="mt-4 inline-flex justify-center items-center px-4 py-2 bg-white text-primary-700 font-medium rounded-md hover:bg-gray-50 border border-gray-300">
                    Hapus Filter
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
