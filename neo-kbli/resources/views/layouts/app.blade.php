<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Applicant Area - NEO KEBULI')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    @include('components.theme-script')
</head>
<body class="bg-background text-primary font-sans antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false }">

    <!-- Topbar -->
    <header class="bg-surface border-b border-border sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <!-- Hamburger button for mobile -->
                <button type="button" @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary rounded-md p-1">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <a href="{{ route('home') }}" class="flex items-center gap-2 outline-none focus-visible:ring-2 focus-visible:ring-primary rounded">
                    <img src="{{ asset('images/logo.png') }}" alt="NEO KEBULI" class="h-8 md:h-10 w-auto object-contain">
                </a>
            </div>
            
            <div class="flex items-center space-x-4">
                <x-ui.theme-toggle />

                <!-- User Dropdown Menu -->
                <x-behavior.dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors ">
                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="hidden sm:block">{{ auth()->user()->name ?? 'Applicant' }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </x-slot>
                    <div class="px-4 py-2 text-xs text-muted border-b border-border">Area Pemohon</div>
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-orange-500 transition-colors">Dashboard</a>
                    <a href="{{ route('user.profile.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-orange-500 transition-colors">Profil</a>
                    <div class="border-t border-border my-1"></div>
                    <form method="POST" action="{{ route('logout') }}" class="px-2 pb-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </x-behavior.dropdown>
            </div>
        </div>
    </header>

    <div class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
        
        <!-- Sidebar container -->
        <div x-cloak :class="sidebarOpen ? 'fixed inset-0 z-50 flex md:hidden' : 'hidden md:block w-64 shrink-0'">
            
            <!-- Overlay background for mobile -->
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/80 md:hidden" @click="sidebarOpen = false" aria-hidden="true"></div>
            
            <!-- Off-canvas drawer (Mobile) & Static Sidebar (Desktop) -->
            <aside :class="sidebarOpen ? 'relative flex w-full max-w-xs flex-1 flex-col bg-surface h-full shadow-xl' : 'w-full bg-surface rounded-xl shadow-sm border border-border overflow-hidden'"
                 x-show="sidebarOpen || window.innerWidth >= 768"
                 x-transition:enter="md:transition-none transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full md:translate-x-0"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="md:transition-none transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 @keydown.window.escape="sidebarOpen = false"
                 @click.outside="if(window.innerWidth < 768) sidebarOpen = false"
                 aria-label="Navigasi Utama">
                 
                 <div class="flex items-center justify-between px-4 py-4 md:hidden border-b border-border bg-slate-50/50">
                     <span class="font-bold text-lg text-slate-800">Menu</span>
                     <button type="button" @click="sidebarOpen = false" class="text-slate-500 hover:text-slate-700 bg-surface rounded p-1 border border-border">
                         <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                     </button>
                 </div>

                <nav class="flex flex-col p-2 space-y-1 h-full overflow-y-auto">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('user.applications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('user.applications.*') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('user.applications.*') ? 'text-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Pengajuan Saya
                    </a>
                    <a href="{{ route('application.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('application.*') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('application.*') ? 'text-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Ajukan Pengajuan
                    </a>
                    <a href="{{ route('user.notifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('user.notifications.*') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('user.notifications.*') ? 'text-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Notifikasi
                    </a>
                    <a href="{{ route('user.profile.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('user.profile.*') ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('user.profile.*') ? 'text-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Profil
                    </a>
                    
                    <div class="border-t border-border my-2"></div>
                    
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Beranda
                    </a>
                </nav>
            </aside>
            <div class="w-14 shrink-0 md:hidden" aria-hidden="true"></div>
        </div>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 min-w-0">
            @if(View::hasSection('breadcrumbs'))
                <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                    @yield('breadcrumbs')
                </nav>
            @endif
            
            @yield('content')
        </main>
    </div>

    <footer class="bg-surface border-t border-border py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <p class="text-sm text-muted">&copy; {{ date('Y') }} NEO KEBULI - Area Pemohon.</p>
            @yield('footer')
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
