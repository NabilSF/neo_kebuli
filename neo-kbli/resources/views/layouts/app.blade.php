<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Neo KBLI - Sistem Manajemen Klasifikasi Terpadu')</title>
    <meta name="description" content="@yield('meta_description', 'Cari dan kelola Klasifikasi Baku Lapangan Usaha Indonesia (KBLI) secara efisien dengan Neo KBLI.')">
    
    <!-- OpenGraph Meta Tags for SEO -->
    <meta property="og:title" content="@yield('title', 'Neo KBLI - Sistem Manajemen Klasifikasi Terpadu')">
    <meta property="og:description" content="@yield('meta_description', 'Cari dan kelola Klasifikasi Baku Lapangan Usaha Indonesia (KBLI) secara efisien dengan Neo KBLI.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        /* Smooth scrolling for anchor links */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-background text-gray-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- Public Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" loading="lazy" class="h-10 w-auto" onerror="this.outerHTML='<div class=\'w-10 h-10 bg-primary-500 text-white rounded-lg flex items-center justify-center font-bold text-xl shadow-sm\'>N</div>'">
                        <span class="font-bold text-2xl tracking-tight text-gray-900">Neo KBLI</span>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary-500 font-medium transition-colors">Beranda</a>
                    <a href="{{ route('search') }}" class="text-gray-600 hover:text-primary-500 font-medium transition-colors">Cari KBLI</a>

                    <a href="{{ route('news.index') }}" class="text-gray-600 hover:text-primary-500 font-medium transition-colors">Berita</a>
                    <a href="{{ route('tracking.index') }}" class="text-gray-600 hover:text-primary-500 font-medium transition-colors">Lacak Status</a>
                    
                    @auth
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('operator'))
                            <a href="{{ route('admin.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium px-4 py-2 rounded-md transition-colors text-sm border border-gray-200">Dasbor</a>
                        @endif
                        <form method="POST" action="{{ route('login.post') === route('login') ? '/logout' : route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-500 font-medium transition-colors">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">Masuk</a>
                        <a href="{{ route('application.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-md transition-colors border border-primary-600 text-sm">Ajukan Sekarang</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle Navigation Menu" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg class="h-6 w-6" x-show="mobileMenuOpen" x-cloak fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white border-b border-gray-200 absolute w-full shadow-lg">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-500 hover:bg-gray-50">Beranda</a>
                <a href="{{ route('search') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-500 hover:bg-gray-50">Cari KBLI</a>

                <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-500 hover:bg-gray-50">Berita</a>
                <a href="{{ route('tracking.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-500 hover:bg-gray-50">Lacak Status</a>
                @auth
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('operator'))
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-500 hover:bg-gray-50">Dasbor</a>
                    @endif
                    <form method="POST" action="{{ route('login.post') === route('login') ? '/logout' : route('logout') }}" class="inline block w-full text-left">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-500 hover:bg-gray-50">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary-500 hover:bg-gray-50">Masuk</a>
                    <a href="{{ route('application.create') }}" class="block px-3 py-2 rounded-md text-base font-medium text-primary-600 hover:bg-primary-50">Ajukan Sekarang</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col">
        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" loading="lazy" class="h-8 w-auto brightness-0 invert opacity-90" onerror="this.outerHTML='<div class=\'w-8 h-8 bg-primary-500 text-white rounded flex items-center justify-center font-bold text-sm\'>N</div>'">
                        <span class="font-bold text-white text-lg">Neo KBLI</span>
                    </div>
                    <p class="text-sm leading-relaxed">Portal resmi pencarian dan manajemen Klasifikasi Baku Lapangan Usaha Indonesia.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white text-sm uppercase tracking-wider mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('search') }}" class="hover:text-white transition-colors">Pencarian KBLI</a></li>
                        <li><a href="{{ route('application.create') }}" class="hover:text-white transition-colors">Pengajuan Permohonan</a></li>
                        <li><a href="{{ route('tracking.index') }}" class="hover:text-white transition-colors">Lacak Status</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white text-sm uppercase tracking-wider mb-4">Informasi</h4>
                    <ul class="space-y-2 text-sm">

                        <li><a href="{{ route('news.index') }}" class="hover:text-white transition-colors">Berita</a></li>
                        <li><a href="{{ route('home') }}#explore" class="hover:text-white transition-colors">Struktur KBLI</a></li>
                        <li><a href="/sitemap.xml" class="hover:text-white transition-colors">Peta Situs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white text-sm uppercase tracking-wider mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li>Email: info@neokbli.go.id</li>
                        <li>Telepon: (021) 1500-587</li>
                        <li class="pt-2 text-xs">Kebijakan Privasi berlaku.</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 text-center text-xs">
                &copy; {{ date('Y') }} Neo KBLI. Hak cipta dilindungi undang-undang.
            </div>
        </div>
    </footer>

</body>
</html>
