@extends('layouts.admin')

@section('title', 'Pengaturan - NEO KEBULI')

@section('content')
<div class="mb-6">
    <x-navigation.page-header 
        title="Pengaturan" 
        description="Kelola pengaturan sistem dan preferensi tampilan."
    />
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1">
        <h3 class="text-lg font-medium text-slate-900">Penampilan</h3>
        <p class="mt-1 text-sm text-slate-500">Sesuaikan tampilan tema aplikasi NEO KEBULI di perangkat Anda.</p>
    </div>
    
    <div class="md:col-span-2">
        <div class="bg-white shadow rounded-lg p-6">
            <h4 class="text-base font-medium text-slate-900 mb-4 dark:text-white">Tema Tampilan (Dark Mode)</h4>
            <div class="space-y-4">
                <x-ui.theme-toggle />
                
                <p class="text-xs text-slate-500 mt-2 dark:text-slate-400">
                    Preferensi disimpan di perangkat ini menggunakan localStorage. Jika belum memilih, tampilan akan mengikuti mode yang sedang aktif di sistem operasi Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
