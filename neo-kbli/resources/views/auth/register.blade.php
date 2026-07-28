<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Neo KBLI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center p-4 py-12">

    <div class="w-full max-w-md">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 mb-6 transition-colors group">
            <svg class="w-4 h-4 mr-1.5 text-gray-400 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>

        <!-- Register Card -->
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden p-8">
            <div class="text-center mb-8">
                <!-- Logo -->
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="h-16 w-auto" onerror="this.outerHTML='<span class=\'text-2xl font-bold text-gray-900\'>Neo KBLI</span>'">
                </div>
                <h1 class="text-xl font-bold text-gray-900">Daftar Akun Baru</h1>
                <p class="text-sm text-gray-500 mt-1">Lengkapi formulir di bawah ini untuk mendaftar</p>
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

            <form action="{{ route('register.post') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 transition-colors bg-white text-gray-900 text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 transition-colors bg-white text-gray-900 text-sm">
                </div>



                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 transition-colors bg-white text-gray-900 text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-primary-500 focus:ring focus:ring-primary-200 transition-colors bg-white text-gray-900 text-sm">
                </div>

                <div class="flex items-start" x-data="{ termsOpen: false }">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </div>
                    <div class="ml-2 text-sm">
                        <label for="terms" class="font-medium text-gray-700">Saya menyetujui <button type="button" @click="termsOpen = true" class="text-primary-600 hover:text-primary-700 underline">Syarat dan Ketentuan</button> serta Kebijakan Privasi yang berlaku</label>
                    </div>
                    
                    <!-- Terms Modal -->
                    <div x-show="termsOpen" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div x-show="termsOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                <div x-show="termsOpen" 
                                     x-transition:enter="ease-out duration-300" 
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                     x-transition:leave="ease-in duration-200" 
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                     @click.outside="termsOpen = false" 
                                     @keydown.escape.window="termsOpen = false"
                                     class="relative transform overflow-hidden rounded-lg bg-white text-left border border-gray-200 transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Syarat dan Ketentuan & Kebijakan Privasi</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 mb-2">1. Penggunaan Data: Data pribadi yang Anda masukkan akan digunakan secara eksklusif untuk keperluan sistem Neo KBLI dan tidak akan dibagikan ke pihak ketiga tanpa persetujuan Anda.</p>
                                            <p class="text-sm text-gray-500 mb-2">2. Kewajiban Pengguna: Anda berkewajiban memberikan data yang valid dan bertanggung jawab penuh atas segala aktivitas di dalam akun Anda.</p>
                                            <p class="text-sm text-gray-500">3. Hak Cipta: Seluruh konten dan struktur KBLI mengacu pada peraturan pemerintah yang berlaku.</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                        <button type="button" @click="termsOpen = false" class="inline-flex w-full justify-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 sm:ml-3 sm:w-auto">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors shadow-sm text-sm">
                    Daftar Akun
                </button>
            </form>
            
            <div class="mt-6 text-center border-t border-gray-100 pt-6">
                <p class="text-sm text-gray-600">Sudah memiliki akun? <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700">Masuk di sini</a></p>
            </div>
        </div>
    </div>

</body>
</html>
