@extends('layouts.app')

@section('title', 'Notifikasi - NEO KEBULI')

@section('breadcrumbs')
    <span>/</span>
    <span class="text-slate-900 font-medium">Notifikasi</span>
@endsection

@section('content')
<div class="w-full">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola dan pantau semua pembaruan pengajuan Anda.</p>
        </div>
        
        <div class="flex items-center gap-3">
            @if($data->unreadCount > 0)
            <form method="POST" action="{{ route('user.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-surface border border-border rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm" aria-label="Tandai semua dibaca">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Tandai Semua Dibaca
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-lg" role="alert">{{ session('success') }}</div>
    @endif

    <!-- Statistik -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <x-ui.card class="p-4 flex flex-col justify-center text-center sm:text-left">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-900">{{ $data->total }}</p>
        </x-ui.card>
        <x-ui.card class="p-4 flex flex-col justify-center text-center sm:text-left border-l-4 border-l-gray-400">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Belum Dibaca</p>
            <p class="text-2xl font-bold text-gray-500">{{ $data->unreadCount }}</p>
        </x-ui.card>
        <x-ui.card class="p-4 flex flex-col justify-center text-center sm:text-left border-l-4 border-l-gray-400">
            <p class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Sudah Dibaca</p>
            <p class="text-2xl font-bold text-gray-500">{{ $data->readCount }}</p>
        </x-ui.card>
    </div>

    <!-- Daftar Notifikasi -->
    <div class="bg-surface rounded-xl border border-border shadow-sm overflow-hidden">
        @if($data->notifications->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900 mb-1">Tidak ada notifikasi</h3>
                <p class="text-slate-500 text-sm">Anda belum menerima notifikasi apapun saat ini.</p>
            </div>
        @else
            <ul class="divide-y divide-border">
                @foreach($data->notifications as $notification)
                <li class="relative p-4 sm:p-6 hover:bg-slate-50/50 transition-colors {{ $notification->unread() ? 'bg-primary/5' : '' }}">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            @if($notification->unread())
                                <div class="w-2.5 h-2.5 bg-primary rounded-full mt-2" aria-hidden="true"></div>
                            @else
                                <div class="w-2.5 h-2.5 bg-slate-300 rounded-full mt-2" aria-hidden="true"></div>
                            @endif
                        </div>
                        
                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-4 mb-1">
                                <p class="text-sm font-semibold {{ $notification->unread() ? 'text-slate-900' : 'text-slate-700' }}">
                                    @php
                                        $url = isset($notification->data['application_id']) 
                                            ? route('user.applications.show', $notification->data['application_id']) 
                                            : (isset($notification->data['url']) ? $notification->data['url'] : route('user.dashboard'));
                                    @endphp
                                    <a href="{{ $url }}" class="hover:text-primary hover:underline focus:outline-none">
                                        {{ $notification->data['message'] ?? 'Pemberitahuan Sistem' }}
                                    </a>
                                </p>
                                <span class="text-xs text-slate-500 whitespace-nowrap flex-shrink-0">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            @if(isset($notification->data['application_id']))
                                <p class="text-sm text-slate-500 mb-2">Terkait Pengajuan ID: <span class="font-medium text-slate-700">#{{ $notification->data['application_id'] }}</span></p>
                            @endif

                            @if($notification->unread())
                                <form method="POST" action="{{ route('user.notifications.mark-read', $notification->id) }}" class="inline-block mt-1 mr-2">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-primary hover:text-primary-600 focus:outline-none" aria-label="Tandai dibaca">
                                        Tandai dibaca
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('user.notifications.destroy', $notification->id) }}" class="inline-block mt-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-600 focus:outline-none" aria-label="Hapus notifikasi">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Pagination -->
    @if($data->notifications->hasPages())
        <div class="mt-6">
            {{ $data->notifications->links() }}
        </div>
    @endif
</div>
@endsection
