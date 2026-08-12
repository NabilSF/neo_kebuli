<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Neo KBLI - Sistem Manajemen Klasifikasi Terpadu')</title>
    <meta name="description" content="@yield('meta_description', 'Cari dan kelola Klasifikasi Baku Lapangan Usaha Indonesia (KBLI) secara efisien dengan Neo KBLI.')">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- OpenGraph Meta Tags for SEO -->
    <meta property="og:title" content="@yield('title', 'Neo KBLI - Sistem Manajemen Klasifikasi Terpadu')">
    <meta property="og:description" content="@yield('meta_description', 'Cari dan kelola Klasifikasi Baku Lapangan Usaha Indonesia (KBLI) secara efisien dengan Neo KBLI.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        /* Smooth scrolling for anchor links */
        html { scroll-behavior: smooth; }
    </style>
    @include('components.theme-script')
</head>
<body class="bg-background text-primary font-sans antialiased min-h-screen flex flex-col">

    <!-- Public Navigation -->
    <nav class="bg-surface border-b border-border sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-sm">
                        <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" loading="lazy" class="h-10 w-auto">
                        <span class="font-bold text-2xl tracking-tight text-slate-900 dark:text-white">Neo KBLI</span>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-orange-500 hover:text-orange-600 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-sm">Beranda</a>
                    <a href="{{ route('search') }}" class="text-orange-500 hover:text-orange-600 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-sm">Cari KBLI</a>
                    <a href="{{ route('news.index') }}" class="text-orange-500 hover:text-orange-600 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-sm">Berita</a>
                    <a href="{{ route('tracking.index') }}" class="text-orange-500 hover:text-orange-600 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-sm">Lacak Status</a>
                    
                    @auth
                        @php
                            $dashRoute = (auth()->user()->hasAnyRole(['Super Admin', 'super-admin', 'Admin', 'Administrator', 'Operator'])) ? route('admin.dashboard') : route('dashboard');
                            $profileRoute = route('user.profile.index');
                        @endphp
                        <a href="{{ $dashRoute }}" class="text-orange-500 hover:text-orange-600 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-sm">Dashboard</a>
                        <a href="{{ $profileRoute }}" class="text-orange-500 hover:text-orange-600 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-sm">Profil</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-orange-500 border border-transparent rounded-md hover:bg-orange-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-orange-500 border border-transparent rounded-md hover:bg-orange-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">Masuk</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle Navigation Menu" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 rounded-md p-1">
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
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-surface border-b border-border absolute w-full shadow-lg">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-orange-500 hover:text-orange-600 hover:bg-slate-50 dark:hover:bg-slate-800/50">Beranda</a>
                <a href="{{ route('search') }}" class="block px-3 py-2 rounded-md text-base font-medium text-orange-500 hover:text-orange-600 hover:bg-slate-50 dark:hover:bg-slate-800/50">Cari KBLI</a>
                <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-orange-500 hover:text-orange-600 hover:bg-slate-50 dark:hover:bg-slate-800/50">Berita</a>
                <a href="{{ route('tracking.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-orange-500 hover:text-orange-600 hover:bg-slate-50 dark:hover:bg-slate-800/50">Lacak Status</a>
                @auth
                    @php
                        $dashRoute = (auth()->user()->hasAnyRole(['Super Admin', 'super-admin', 'Admin', 'Administrator', 'Operator'])) ? route('admin.dashboard') : route('dashboard');
                        $profileRoute = route('user.profile.index');
                    @endphp
                    <a href="{{ $dashRoute }}" class="block px-3 py-2 rounded-md text-base font-medium text-orange-500 hover:text-orange-600 hover:bg-slate-50 dark:hover:bg-slate-800/50">Dashboard</a>
                    <a href="{{ $profileRoute }}" class="block px-3 py-2 rounded-md text-base font-medium text-orange-500 hover:text-orange-600 hover:bg-slate-50 dark:hover:bg-slate-800/50">Profil</a>
                    <form method="POST" action="{{ route('logout') }}" class="block w-full text-left mt-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-white bg-orange-500 hover:bg-orange-600 flex items-center gap-2">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-orange-500 hover:bg-orange-600 flex items-center justify-center mt-2">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow flex flex-col">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="bg-surface text-slate-500 dark:text-slate-400 py-12 mt-auto border-t border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" loading="lazy" class="h-8 w-auto">
                        <span class="font-bold text-slate-900 dark:text-white text-lg">Neo KBLI</span>
                    </div>
                    <p class="text-sm leading-relaxed">Portal resmi pencarian dan manajemen Klasifikasi Baku Lapangan Usaha Indonesia.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white text-sm uppercase tracking-wider mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('search') }}" class="hover:text-orange-500 transition-colors">Pencarian KBLI</a></li>
                        <li><a href="{{ route('application.create') }}" class="hover:text-orange-500 transition-colors">Pengajuan Permohonan</a></li>
                        <li><a href="{{ route('tracking.index') }}" class="hover:text-orange-500 transition-colors">Lacak Status</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white text-sm uppercase tracking-wider mb-4">Informasi</h4>
                    <ul class="space-y-2 text-sm">

                        <li><a href="{{ route('news.index') }}" class="hover:text-orange-500 transition-colors">Berita</a></li>
                        <li><a href="{{ route('home') }}#explore" class="hover:text-orange-500 transition-colors">Struktur KBLI</a></li>
                        <li><a href="/sitemap.xml" class="hover:text-orange-500 transition-colors">Peta Situs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white text-sm uppercase tracking-wider mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li>Email: info@neokbli.go.id</li>
                        <li>Telepon: (021) 1500-587</li>
                        <li class="pt-2 text-xs text-slate-400 dark:text-slate-500">Kebijakan Privasi berlaku.</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-border pt-6 mt-6 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500 dark:text-slate-400">
                <div>&copy; {{ date('Y') }} Neo KBLI. Hak cipta dilindungi undang-undang.</div>
                
                <div class="mt-4 md:mt-0 flex items-center gap-3">
                    <span class="text-slate-500 dark:text-slate-400 mr-2">Tema:</span>
                    <x-ui.theme-toggle />
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
