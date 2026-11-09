<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server | NEO KEBULI</title>
    <meta name="description" content="Terjadi kesalahan internal pada server. Kami sedang menanganinya.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg text-center">
        <!-- Kode Error -->
        <div class="relative mb-8">
            <div class="text-[10rem] font-black text-red-100 leading-none select-none">500</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-red-500 rounded-2xl flex items-center justify-center shadow-xl shadow-red-200">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pesan -->
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Kesalahan Server Internal</h1>
        <p class="text-gray-500 text-base mb-2">Terjadi kesalahan tak terduga pada server kami. Kami memohon maaf atas gangguan ini.</p>
        <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-2 inline-block mb-6">Tim teknis kami telah diberitahu dan sedang menangani masalah ini.</p>

        <!-- Tindakan -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-3 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Kembali ke Beranda
            </a>
            <button onclick="window.location.reload()" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-medium px-6 py-3 rounded-xl border border-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Coba Lagi
            </button>
        </div>

        <!-- Logo -->
        <div class="mt-12 flex items-center justify-center gap-2 opacity-50">
            <span class="text-sm font-semibold text-gray-400">NEO KEBULI</span>
        </div>
    </div>
</body>
</html>
