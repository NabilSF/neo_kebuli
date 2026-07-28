<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Terlalu Banyak Permintaan | Neo KBLI</title>
    <meta name="description" content="Anda telah mengirim terlalu banyak permintaan. Silakan tunggu sebentar.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg text-center">
        <!-- Kode Error -->
        <div class="relative mb-8">
            <div class="text-[10rem] font-black text-yellow-100 leading-none select-none">429</div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-yellow-500 rounded-2xl flex items-center justify-center shadow-xl shadow-yellow-200">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pesan -->
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Terlalu Banyak Permintaan</h1>
        <p class="text-gray-500 text-base mb-2">Anda telah melakukan terlalu banyak permintaan dalam waktu singkat.</p>
        <p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 inline-block mb-6">Silakan tunggu beberapa saat sebelum mencoba kembali.</p>

        <!-- Tindakan -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-3 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-medium px-6 py-3 rounded-xl border border-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </button>
        </div>

        <!-- Logo -->
        <div class="mt-12 flex items-center justify-center gap-2 opacity-50">
            <span class="text-sm font-semibold text-gray-400">Neo KBLI</span>
        </div>
    </div>
</body>
</html>
