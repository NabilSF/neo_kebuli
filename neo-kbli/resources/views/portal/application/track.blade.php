@extends('layouts.guest')

@section('title', 'Lacak Status Permohonan')

@section('content')
<div class="bg-gray-50 dark:bg-slate-900 border-b border-gray-200 dark:border-slate-800 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 flex items-center justify-center mx-auto mb-6 shadow-xs">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Lacak Permohonan Anda</h1>
        <p class="text-lg text-gray-600 dark:text-slate-400 mb-10">Masukkan kode pelacakan Anda untuk melihat status saat ini dan riwayat pengajuan klasifikasi bisnis Anda.</p>

        @if ($errors->any())
            <div class="mb-8 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 p-4 rounded-xl border border-red-200 dark:border-red-900/50 text-left">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tracking.show') }}" method="GET" class="relative max-w-2xl mx-auto group">
            <div class="flex rounded-xl overflow-hidden bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-700 transition-colors focus-within:border-orange-500 dark:focus-within:border-orange-500 shadow-sm">
                <input type="text" name="code" required class="block w-full pl-6 pr-4 py-4 text-base md:text-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 bg-transparent border-none uppercase tracking-wider font-mono font-bold focus:ring-0" placeholder="KBLI-YYYYMMDD-XXXX">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 font-bold text-base transition-colors flex-shrink-0 cursor-pointer">
                    Lacak Status
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 flex-grow">
    <!-- Empty space for layout balance -->
</div>
@endsection
