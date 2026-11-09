@extends('layouts.admin')

@section('title', 'Dasbor Eksekutif - NEO KEBULI')

@section('content')
<main class="w-full">
    {{-- A. Greeting --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Selamat datang, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">
            <span class="font-medium text-primary">{{ auth()->user()->roles->first()?->name ?? 'Admin' }}</span>
            <span class="mx-2 text-slate-300">•</span>
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </p>
    </div>

    {{-- B. Quick Statistics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        {{-- Pengajuan --}}
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Pengajuan</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($statistics->totalApplications) }}</p>
        </div>
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5 border-l-4 border-l-info">
            <p class="text-xs font-semibold text-info uppercase tracking-wider mb-1">Menunggu Review</p>
            <p class="text-3xl font-bold text-info">{{ number_format($statistics->totalSubmitted + $statistics->totalUnderReview) }}</p>
        </div>
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5 border-l-4 border-l-success">
            <p class="text-xs font-semibold text-success uppercase tracking-wider mb-1">Disetujui</p>
            <p class="text-3xl font-bold text-success">{{ number_format($statistics->totalApproved) }}</p>
        </div>
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5 border-l-4 border-l-danger">
            <p class="text-xs font-semibold text-danger uppercase tracking-wider mb-1">Ditolak / Revisi</p>
            <p class="text-3xl font-bold text-danger">{{ number_format($statistics->totalRejected + $statistics->totalRevision) }}</p>
        </div>
        
        {{-- Master Data --}}
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Pengguna</p>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($statistics->totalUsers) }}</p>
        </div>
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Katalog KBLI</p>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($statistics->totalKbli) }}</p>
        </div>
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Berita Aktif</p>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($statistics->totalNews) }}</p>
        </div>
        <div class="bg-surface rounded-xl shadow-sm border border-border p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Approval Rate</p>
            <p class="text-2xl font-bold text-slate-900">{{ $statistics->approvalRate }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- C. Quick Actions --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-surface rounded-xl shadow-sm border border-border p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Aksi Cepat</h2>
                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('admin.applications.index') }}" class="flex items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-100 group">
                        <div class="bg-primary/10 p-2 rounded-md text-primary mr-3 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Review Pengajuan</span>
                    </a>
                    <a href="{{ route('admin.kbli.items.index') }}" class="flex items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-100 group">
                        <div class="bg-primary/10 p-2 rounded-md text-primary mr-3 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Kelola Katalog KBLI</span>
                    </a>
                    <a href="{{ route('admin.news.index') }}" class="flex items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-100 group">
                        <div class="bg-primary/10 p-2 rounded-md text-primary mr-3 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Kelola Berita</span>
                    </a>
                    @if(auth()->user()->hasRole(['super-admin', 'Super Admin']))
                    <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-lg transition-colors border border-slate-100 group">
                        <div class="bg-primary/10 p-2 rounded-md text-primary mr-3 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Kelola Pengguna</span>
                    </a>
                    @endif

                </div>
            </div>

            {{-- G. Performance Widget --}}
            <div class="bg-surface rounded-xl shadow-sm border border-border p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Performa Hari Ini</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Total Upload Dokumen</span>
                        <span class="text-sm font-bold text-slate-900">{{ $performanceToday['uploads'] }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ min(100, $performanceToday['uploads'] * 5) }}%"></div>
                    </div>
                    
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm text-slate-600">Disetujui</span>
                        <span class="text-sm font-bold text-success">{{ $performanceToday['approvals'] }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm text-slate-600">Pending Review Aktif</span>
                        <span class="text-sm font-bold text-warning">{{ $performanceToday['pending'] }}</span>
                    </div>
                </div>
            </div>

            {{-- H. System Health --}}
            <div class="bg-surface rounded-xl shadow-sm border border-border p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Status Sistem</h2>
                <ul class="space-y-3">
                    <li class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Database</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $systemHealth['database'] === 'Sehat' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                            {{ $systemHealth['database'] }}
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Storage</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $systemHealth['storage'] === 'Sehat' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                            {{ $systemHealth['storage'] }}
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Cache</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $systemHealth['cache'] === 'Sehat' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                            {{ $systemHealth['cache'] }}
                        </span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-sm text-slate-600">Queue Worker</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $systemHealth['queue'] === 'Sehat' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                            {{ $systemHealth['queue'] }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- D. Recent Applications & F. Analytics --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Analytics Trend Chart --}}
            <div class="bg-surface rounded-xl shadow-sm border border-border p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Tren Permohonan KBLI</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Statistik permohonan masuk 6 bulan terakhir</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                            Live Data
                        </span>
                    </div>
                </div>

                <!-- SVG Line Chart (Reused from Previous) -->
                <div class="relative w-full h-[200px] mt-2">
                    <svg class="w-full h-full overflow-visible" viewBox="0 0 600 220" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="primaryAreaGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#f97316" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#f97316" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <g class="text-[10px] fill-slate-400 stroke-slate-100" stroke-dasharray="4 4">
                            <line x1="30" y1="40" x2="580" y2="40" stroke-width="1"/>
                            <text x="20" y="43" text-anchor="end" stroke="none">15</text>
                            <line x1="30" y1="80" x2="580" y2="80" stroke-width="1"/>
                            <text x="20" y="83" text-anchor="end" stroke="none">10</text>
                            <line x1="30" y1="120" x2="580" y2="120" stroke-width="1"/>
                            <text x="20" y="123" text-anchor="end" stroke="none">5</text>
                            <line x1="30" y1="160" x2="580" y2="160" stroke-width="1"/>
                            <text x="20" y="163" text-anchor="end" stroke="none">0</text>
                        </g>
                        <path d="M 50 144 L 146 112 L 242 134 L 338 101 L 434 80 L 530 58 L 530 160 L 50 160 Z" fill="url(#primaryAreaGradient)" />
                        <path d="M 50 144 L 146 112 L 242 134 L 338 101 L 434 80 L 530 58" fill="none" stroke="#f97316" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                        <!-- X-Axis Labels -->
                        <g class="text-[11px] font-medium fill-slate-500">
                            <text x="50" y="185" text-anchor="middle">Jan</text>
                            <text x="146" y="185" text-anchor="middle">Feb</text>
                            <text x="242" y="185" text-anchor="middle">Mar</text>
                            <text x="338" y="185" text-anchor="middle">Apr</text>
                            <text x="434" y="185" text-anchor="middle">Mei</text>
                            <text x="530" y="185" text-anchor="middle">Jun</text>
                        </g>
                    </svg>
                </div>
            </div>

            {{-- Recent Applications Table --}}
            <div class="bg-surface rounded-xl shadow-sm border border-border overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-slate-50/50 flex justify-between items-center">
                    <h2 class="text-sm font-semibold text-slate-900">10 Pengajuan Terbaru</h2>
                    <a href="{{ route('admin.applications.index') }}" class="text-xs font-medium text-primary hover:text-primary-600">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tracking / Pemohon</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-border">
                            @forelse($recentApplications as $app)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-slate-900">{{ $app['tracking_code'] }}</div>
                                    <div class="text-sm text-slate-500">{{ $app['userName'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badgeColor = match($app['status']) {
                                            'submitted', 'under_review' => 'bg-info/10 text-info',
                                            'need_revision' => 'bg-warning/10 text-warning',
                                            'approved' => 'bg-success/10 text-success',
                                            'rejected' => 'bg-danger/10 text-danger',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                        
                                        $label = match($app['status']) {
                                            'draft' => 'Draf',
                                            'submitted' => 'Diajukan',
                                            'under_review' => 'Sedang Ditinjau',
                                            'need_revision' => 'Perlu Revisi',
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                            'archived' => 'Diarsipkan',
                                            default => $app['status']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $app['created_at'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.applications.show', $app['id']) }}" class="text-primary hover:text-primary-600 bg-primary/10 hover:bg-primary/20 px-3 py-1.5 rounded-md transition-colors" title="Lihat Detail {{ $app['tracking_code'] }}">Review</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada data pengajuan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- E. Recent Activities Timeline --}}
    <div class="bg-surface rounded-xl shadow-sm border border-border p-6 mb-8">
        <h2 class="text-sm font-semibold text-slate-900 mb-6">Aktivitas Sistem Terbaru (10 Log)</h2>
        @if($recentActivities->isEmpty())
            <p class="text-sm text-slate-500 text-center py-4">Belum ada aktivitas terekam.</p>
        @else
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($recentActivities as $index => $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center ring-8 ring-white border border-slate-200">
                                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div>
                                        <p class="text-sm text-slate-600">
                                            <span class="font-medium text-slate-900">{{ $activity->user->name ?? 'Sistem' }}</span> 
                                            {{ strtolower($activity->description) }}
                                        </p>
                                    </div>
                                    <div class="text-right text-sm whitespace-nowrap text-slate-500">
                                        <time datetime="{{ $activity->created_at }}">{{ $activity->created_at?->diffForHumans() }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</main>
@endsection
