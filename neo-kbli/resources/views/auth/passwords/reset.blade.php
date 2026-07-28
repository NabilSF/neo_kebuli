<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - Neo KBLI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center p-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="h-16 w-auto" onerror="this.outerHTML='<span class=\'text-2xl font-bold text-gray-900\'>Neo KBLI</span>'">
                </div>
                <h1 class="text-xl font-bold text-gray-900">Atur Ulang Kata Sandi</h1>
                <p class="text-sm text-gray-500 mt-1">Masukkan kata sandi baru Anda</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-lg text-sm border border-red-100">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" required readonly
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-gray-500 text-sm">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" required autofocus
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 transition-colors bg-white text-gray-900 text-sm">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 transition-colors bg-white text-gray-900 text-sm">
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
                    Simpan Kata Sandi
                </button>
            </form>
        </div>
    </div>
</body>
</html>
