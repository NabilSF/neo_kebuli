<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dasbor Admin') - Neo KBLI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col shrink-0">
        <div class="flex items-center justify-center h-16 border-b border-gray-200 px-6 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" loading="lazy" class="h-8 w-auto" onerror="this.outerHTML='<span class=\'text-xl font-bold text-gray-900 tracking-tight\'>Neo KBLI <span class=\'text-primary-600\'>Admin</span></span>'">
                <span class="text-xl font-bold text-gray-900 tracking-tight">Neo KBLI</span>
            </a>
        </div>
        
        <div class="overflow-y-auto overflow-x-hidden flex-grow py-4">
            <ul class="flex flex-col space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-50 text-gray-700 hover:text-gray-900 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-primary-600 text-primary-700 bg-primary-50 font-medium' : 'border-transparent' }} pr-6 transition-colors">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </span>
                        <span class="ml-3 text-sm tracking-wide truncate">Dasbor</span>
                    </a>
                </li>
                
                <li class="px-5 mt-6 mb-2">
                    <div class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Data Master</div>
                </li>
                <li>
                    <a href="{{ route('admin.kbli.categories') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-50 text-gray-700 hover:text-gray-900 border-l-4 {{ request()->routeIs('admin.kbli.categories') ? 'border-primary-600 text-primary-700 bg-primary-50 font-medium' : 'border-transparent' }} pr-6 transition-colors">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </span>
                        <span class="ml-3 text-sm tracking-wide truncate">Kategori Utama</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kbli.sections') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-50 text-gray-700 hover:text-gray-900 border-l-4 {{ request()->routeIs('admin.kbli.sections') ? 'border-primary-600 text-primary-700 bg-primary-50 font-medium' : 'border-transparent' }} pr-6 transition-colors">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </span>
                        <span class="ml-3 text-sm tracking-wide truncate">Seksi Klasifikasi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kbli.items') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-50 text-gray-700 hover:text-gray-900 border-l-4 {{ request()->routeIs('admin.kbli.items') ? 'border-primary-600 text-primary-700 bg-primary-50 font-medium' : 'border-transparent' }} pr-6 transition-colors">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </span>
                        <span class="ml-3 text-sm tracking-wide truncate">Kode KBLI</span>
                    </a>
                </li>



                <li class="px-5 mt-6 mb-2">
                    <div class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Konten & Berita</div>
                </li>
                <li>
                    <a href="{{ route('admin.news.index') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-50 text-gray-700 hover:text-gray-900 border-l-4 {{ request()->routeIs('admin.news.*') ? 'border-primary-600 text-primary-700 bg-primary-50 font-medium' : 'border-transparent' }} pr-6 transition-colors">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </span>
                        <span class="ml-3 text-sm tracking-wide truncate">Berita & Pengumuman</span>
                    </a>
                </li>

                @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp
                @if($user->hasRole('super-admin'))
                <li class="px-5 mt-6 mb-2">
                    <div class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Administrasi</div>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="relative flex flex-row items-center h-10 focus:outline-none hover:bg-gray-50 text-gray-700 hover:text-gray-900 border-l-4 {{ request()->routeIs('admin.users.*') ? 'border-primary-600 text-primary-700 bg-primary-50 font-medium' : 'border-transparent' }} pr-6 transition-colors">
                        <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </span>
                        <span class="ml-3 text-sm tracking-wide truncate">Manajemen Pengguna</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="h-16 shrink-0 flex items-center justify-between px-6 bg-white border-b border-gray-200">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-semibold text-gray-800 truncate">@yield('title')</h2>
                    <span class="text-gray-300 hidden sm:inline">|</span>
                    <a href="{{ route('home') }}" class="hidden sm:inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors group">
                        <svg class="w-4 h-4 mr-1 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Ke Portal Publik
                    </a>
                </div>
            </div>
            <div class="flex items-center space-x-4 shrink-0">
                <div class="hidden sm:flex items-center text-sm font-medium text-gray-600">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ Auth::user()->name ?? 'Administrator' }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium focus:outline-none transition-colors">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Page Content -->
        <main class="flex-1 p-6 md:p-8 overflow-x-hidden overflow-y-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
