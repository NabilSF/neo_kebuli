<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Neo KBLI') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('components.theme-script')
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen"
      x-data="{
          collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          mobileOpen: false,
          toggleSidebar() {
              this.collapsed = !this.collapsed;
              localStorage.setItem('sidebar_collapsed', this.collapsed);
          }
      }">
    <!-- Skip Navigation -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:p-4 focus:bg-surface focus:text-primary rounded-br-md shadow-md">
        Lompat ke konten utama
    </a>

    <!-- Flash Messages Region -->
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-full max-w-sm pointer-events-none" aria-live="polite">
        @if (session('success'))
            <x-behavior.toast variant="success" title="Berhasil" :description="session('success')" />
        @endif
        @if (session('error'))
            <x-behavior.toast variant="danger" title="Kesalahan" :description="session('error')" />
        @endif
    </div>

    <!-- Mobile Sidebar Drawer Structure -->
    <div x-show="mobileOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
        <div x-show="mobileOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs"
             @click="mobileOpen = false"></div>

        <div class="fixed inset-0 flex">
            <div x-show="mobileOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative mr-16 flex w-full max-w-xs flex-1 bg-surface"
                 @click.outside="mobileOpen = false">
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" @click="mobileOpen = false" class="-m-2.5 p-2.5 text-white">
                        <span class="sr-only">Tutup bilah sisi</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <x-navigation.sidebar />
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:flex-col lg:border-r lg:border-border lg:bg-surface transition-all duration-200"
           :class="collapsed ? 'lg:w-20' : 'lg:w-72'">
        <x-navigation.sidebar />
    </aside>

    <!-- Main Workspace -->
    <div class="flex flex-col min-h-screen transition-all duration-200"
         :class="collapsed ? 'lg:pl-20' : 'lg:pl-72'">
        <!-- Top Navigation -->
        <x-navigation.topbar />

        <!-- Page Header & Content -->
        <main id="main-content" class="flex-1 py-8 " tabindex="-1">
            <div class="px-4 sm:px-6 lg:px-8 mx-auto max-w-7xl">
                <!-- Workspace Header Region -->
                @if(isset($header))
                    <div class="mb-8">
                        {{ $header }}
                    </div>
                @elseif(View::hasSection('header'))
                    <div class="mb-8">
                        @yield('header')
                    </div>
                @endif

                <!-- Content Region -->
                <div class="w-full">
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
