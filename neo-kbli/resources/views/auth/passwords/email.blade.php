<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Neo KBLI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center p-4 py-12">
    <div class="w-full max-w-md">
        <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 mb-6 transition-colors group">
            <svg class="w-4 h-4 mr-1.5 text-gray-400 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Masuk
        </a>
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="h-16 w-auto" onerror="this.outerHTML='<span class=\'text-2xl font-bold text-gray-900\'>Neo KBLI</span>'">
                </div>
                <h1 class="text-xl font-bold text-gray-900">Lupa Kata Sandi</h1>
                <p class="text-sm text-gray-500 mt-1">Masukkan email Anda untuk menerima tautan atur ulang sandi</p>
            </div>

            @if (session('status'))
                <div class="mb-6 bg-green-50 text-green-700 p-4 rounded-lg text-sm border border-green-100">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-lg text-sm border border-red-100">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 transition-colors bg-white text-gray-900 text-sm">
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
                    Kirim Tautan Atur Ulang Sandi
                </button>
            </form>
        </div>
    </div>
</body>
</html>
