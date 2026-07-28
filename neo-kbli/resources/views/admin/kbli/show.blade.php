@extends('layouts.admin')

@section('title', 'Detail KBLI')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.kbli.items') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-primary-600">
        <svg class="mr-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Daftar Item
    </a>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center gap-3">
            <span class="font-mono text-base font-bold text-gray-900 bg-white border border-gray-300 px-3 py-1 rounded-md">{{ $item->code }}</span>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mt-2">{{ $item->name }}</h2>
    </div>

    <!-- Content -->
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Left col (Description) -->
            <div class="md:col-span-2">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Deskripsi</h3>
                @if($item->description)
                <div class="text-gray-800 text-sm leading-relaxed">
                    {!! nl2br(e($item->description)) !!}
                </div>
                @else
                <p class="text-sm text-gray-400 italic">Tidak ada deskripsi tersedia.</p>
                @endif
            </div>

            <!-- Right col (Hierarchy) -->
            <div class="bg-gray-50 rounded-md border border-gray-200 p-5">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Hierarki Klasifikasi</h3>

                @if($item->section && $item->section->category)
                <div class="text-sm">
                    <div class="text-gray-500 text-xs mb-0.5">Kategori {{ $item->section->category->code }}</div>
                    <div class="font-medium text-gray-800 mb-3">{{ $item->section->category->name }}</div>

                    <div class="ml-3 border-l-2 border-gray-300 pl-3 py-1">
                        <div class="text-gray-500 text-xs mb-0.5">Seksi {{ $item->section->code }}</div>
                        <div class="font-medium text-gray-800">{{ $item->section->name }}</div>
                    </div>
                </div>
                @else
                <span class="text-sm text-gray-400 italic">Data hierarki tidak lengkap.</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
