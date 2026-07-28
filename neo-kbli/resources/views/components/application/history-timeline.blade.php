@props(['histories'])

<div class="flow-root">
    <ul role="list" class="-mb-8">
        @forelse($histories as $history)
        <li>
            <div class="relative pb-8">
                @if(!$loop->last)
                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                @endif
                <div class="relative flex space-x-3">
                    <div>
                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                            {{ $history->to_status === 'approved' ? 'bg-green-500' : 
                               ($history->to_status === 'rejected' ? 'bg-red-500' : 
                               ($history->to_status === 'under_review' ? 'bg-yellow-500' : 'bg-gray-400')) }}">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                            <p class="text-sm text-gray-500">
                                <span class="font-medium text-gray-900">{{ $history->actor->name ?? 'Tamu / Sistem' }}</span>
                                mengubah status menjadi
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
                                <span class="font-bold text-gray-900 uppercase">{{ $tlLabel }}</span>
                            </p>
                            @if($history->notes)
                            <p class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                {{ $history->notes }}
                            </p>
                            @endif
                        </div>
                        <div class="text-right text-xs whitespace-nowrap text-gray-400 flex flex-col items-end">
                            <time datetime="{{ $history->created_at }}">{{ $history->created_at->format('d M Y') }}</time>
                            <span class="mt-1">{{ $history->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        @empty
        <li class="text-sm text-gray-500">Belum ada riwayat.</li>
        @endforelse
    </ul>
</div>
