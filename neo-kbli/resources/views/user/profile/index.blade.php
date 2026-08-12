@php
    $isAdmin = auth()->user()->hasAnyRole(['Super Admin', 'super-admin', 'Admin', 'Administrator', 'Operator']);
    $layout = $isAdmin ? 'layouts.admin' : 'layouts.app';
@endphp
@extends($layout)

@section('title', 'Profil Saya - Neo KBLI')

@section('breadcrumbs')
    <span>/</span>
    <span class="text-slate-900 font-medium">Profil Saya</span>
@endsection

@section('content')
<div class="w-full">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Profil Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola informasi akun dan pengaturan keamanan Anda.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg" role="alert">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Info Profil Utama & Statistik -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Profil Singkat -->
            <x-ui.card class="overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-24 h-24 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold border-4 border-white shadow-sm">
                        {{ strtoupper(substr($data->user->name, 0, 1)) }}
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 truncate" title="{{ $data->user->name }}">{{ $data->user->name }}</h2>
                    <p class="text-sm text-slate-500 mb-4 truncate" title="{{ $data->user->email }}">{{ $data->user->email }}</p>
                    
                    <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium mb-4">
                        {{ $data->user->roles->first()?->name ?? 'Pemohon' }}
                    </div>
                    
                    <div class="border-t border-border pt-4 mt-2">
                        <p class="text-xs text-slate-400">Anggota sejak {{ $data->user->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Statistik (Hanya untuk User) -->
            @if(!$isAdmin)
            <x-ui.card>
                <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                    <h3 class="font-semibold text-slate-900">Statistik Pengajuan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs text-slate-500 mb-1">Total</p>
                            <p class="text-xl font-bold text-slate-900">{{ $data->totalApplications ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                            <p class="text-xs text-emerald-600 mb-1">Disetujui</p>
                            <p class="text-xl font-bold text-emerald-700">{{ $data->approvedApplications ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-lg border border-amber-100">
                            <p class="text-xs text-amber-600 mb-1">Direvisi</p>
                            <p class="text-xl font-bold text-amber-700">{{ $data->revisionApplications ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                            <p class="text-xs text-red-600 mb-1">Ditolak</p>
                            <p class="text-xl font-bold text-red-700">{{ $data->rejectedApplications ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
            @endif
        </div>

        <!-- Kolom Kanan: Form Update & Aktivitas -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Informasi Akun -->
            <x-ui.card>
                <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                    <h3 class="font-semibold text-slate-900">Informasi Akun</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('user.profile.update') }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $data->user->name) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required autocomplete="name">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $data->user->email) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required autocomplete="email">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Nomor Handphone</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', $data->user->phone) }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" autocomplete="tel">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.card>

            <!-- Pengaturan Tema -->
            <x-ui.card>
                <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                    <h3 class="font-semibold text-slate-900">Penampilan</h3>
                    <p class="text-sm text-slate-500 mt-1">Sesuaikan tema aplikasi (Dark Mode).</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <x-ui.theme-toggle />
                        
                        <p class="text-xs text-slate-500 mt-2">
                            Preferensi disimpan menggunakan localStorage. Jika belum memilih, tampilan akan mengikuti mode yang sedang aktif di sistem operasi Anda.
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Ganti Password -->
            <x-ui.card>
                <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                    <h3 class="font-semibold text-slate-900">Keamanan</h3>
                    <p class="text-sm text-slate-500 mt-1">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('user.password.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4 mb-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">Password Saat Ini</label>
                                <input type="password" name="current_password" id="current_password" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required autocomplete="current-password">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                                <input type="password" name="password" id="password" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required autocomplete="new-password">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required autocomplete="new-password">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-colors">
                                Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.card>

            <!-- Aktivitas Terakhir -->
            @if(!$isAdmin)
            <x-ui.card>
                <div class="px-6 py-4 border-b border-border bg-slate-50/50">
                    <h3 class="font-semibold text-slate-900">Aktivitas Terakhir</h3>
                </div>
                <div class="p-6">
                    @if($data->recentActivities->isEmpty())
                        <p class="text-sm text-slate-500 text-center py-4">Belum ada aktivitas yang tercatat.</p>
                    @else
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach($data->recentActivities as $index => $activity)
                                <li>
                                    <div class="relative pb-8">
                                        @if($index !== $data->recentActivities->count() - 1)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-slate-500">{{ $activity->action }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-slate-500">
                                                    <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->diffForHumans() }}</time>
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
            </x-ui.card>
            @endif
        </div>
    </div>
</div>
@endsection
