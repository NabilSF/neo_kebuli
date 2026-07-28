@extends('layouts.admin')

@section('title', 'Dasbor')

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Dasbor</h2>
    <p class="text-sm text-gray-500 mt-0.5">Selamat datang, {{ Auth::user()->name }}. Berikut ringkasan data sistem.</p>
</div>

{{-- Summary Cards --}}
@php
    $totalCategories = \App\Models\KbliCategory::count();
    $totalSections   = \App\Models\KbliSection::count();
    $totalNews       = \App\Models\News::count();
@endphp
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white border border-gray-200 rounded-md p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kategori</p>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalCategories) }}</p>
        <p class="text-xs text-gray-400 mt-1">Kategori utama KBLI</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-md p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Seksi</p>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalSections) }}</p>
        <p class="text-xs text-gray-400 mt-1">Seksi klasifikasi</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-md p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kode KBLI</p>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalItems) }}</p>
        <p class="text-xs text-gray-400 mt-1">Kode aktif dalam sistem</p>
    </div>
    <div class="bg-white border border-gray-200 rounded-md p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Permohonan</p>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalApplications) }}</p>
        @if($pendingApplications > 0)
        <p class="text-xs text-yellow-600 mt-1">{{ $pendingApplications }} menunggu tinjauan</p>
        @else
        <p class="text-xs text-gray-400 mt-1">Semua permohonan</p>
        @endif
    </div>
    <div class="bg-white border border-gray-200 rounded-md p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Berita</p>
        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalNews) }}</p>
        <p class="text-xs text-gray-400 mt-1">Artikel & pengumuman</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Quick Links --}}
    <div class="bg-white border border-gray-200 rounded-md p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 border-b border-gray-100 pb-2">Akses Cepat</h3>
        <ul class="space-y-2">
            <li>
                <a href="{{ route('admin.applications.index') }}" class="flex items-center justify-between text-sm text-gray-700 hover:text-primary-700 py-1.5 border-b border-gray-50">
                    <span>Antrian Permohonan</span>
                    @if($pendingApplications > 0)
                    <span class="text-xs font-semibold text-yellow-700 bg-yellow-50 border border-yellow-200 px-2 py-0.5 rounded">{{ $pendingApplications }} baru</span>
                    @else
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.news.index') }}" class="flex items-center justify-between text-sm text-gray-700 hover:text-primary-700 py-1.5 border-b border-gray-50">
                    <span>Berita & Pengumuman</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.kbli.items') }}" class="flex items-center justify-between text-sm text-gray-700 hover:text-primary-700 py-1.5 border-b border-gray-50">
                    <span>Kode KBLI</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </li>
            @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp
            @if($user->hasRole('super-admin'))
            <li>
                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between text-sm text-gray-700 hover:text-primary-700 py-1.5">
                    <span>Manajemen Pengguna</span>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </li>
            @endif
        </ul>
    </div>

    {{-- Recent Activity --}}
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-md p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 border-b border-gray-100 pb-2">Aktivitas Terbaru</h3>
        <div class="space-y-3">
            @forelse($recentActivities as $activity)
            <div class="flex items-start gap-3 py-2 border-b border-gray-50 last:border-0">
                <div class="w-1.5 h-1.5 rounded-full bg-primary-400 mt-2 shrink-0"></div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800">{{ $activity->user->name ?? 'Sistem' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $activity->description }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 italic py-4 text-center">Belum ada aktivitas terbaru.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
