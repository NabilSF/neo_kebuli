@extends('layouts.guest')

@section('title', 'Lacak Status Permohonan')

@section('content')
<div class="bg-gray-50 border-b border-gray-200 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-16 h-16 bg-white rounded-md border border-gray-200 flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Lacak Permohonan Anda</h1>
        <p class="text-lg text-gray-600 mb-10">Masukkan kode pelacakan Anda untuk melihat status saat ini dan riwayat pengajuan klasifikasi bisnis Anda.</p>

        @if ($errors->any())
            <div class="mb-8 bg-red-50 text-red-700 p-4 rounded-md border border-red-100 text-left">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tracking.show') }}" method="GET" class="relative max-w-2xl mx-auto group">
            <div class="flex rounded-md overflow-hidden bg-white border border-gray-300 transition-colors focus-within: focus-within: focus-within:border-primary-500">
                <input type="text" name="code" required class="block w-full pl-6 pr-4 py-5 text-lg text-gray-900 placeholder-gray-400  border-none uppercase tracking-wider font-mono font-bold" placeholder="KBLI-YYYYMMDD-XXXX">
                <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-5 font-semibold text-lg transition-colors flex-shrink-0">
                    Lacak Status
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white flex-grow">
    <!-- Empty space for layout balance -->
</div>
@endsection
