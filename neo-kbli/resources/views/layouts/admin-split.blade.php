@props(['title', 'description', 'breadcrumbs', 'toolbar', 'navigation', 'actions', 'aside', 'footer', 'variant' => 'review', 'sidebarCollapsed' => true])

@php
    $leftWidth = 'lg:w-7/10';
    $rightWidth = 'lg:w-3/10';
    
    if ($variant === 'inspection' || $variant === 'editor') {
        $leftWidth = 'lg:w-3/4';
        $rightWidth = 'lg:w-1/4';
    } elseif ($variant === 'comparison') {
        $leftWidth = 'lg:w-1/2';
        $rightWidth = 'lg:w-1/2';
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Tinjauan - ' . config('app.name', 'Neo KBLI') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans text-slate-800 bg-background antialiased min-h-screen flex overflow-hidden">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:p-4 focus:bg-surface focus:text-primary z-50    ke konten utama</a>

    <div class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden" aria-hidden="true" id="mobile-drawer-overlay"></div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 {{ $sidebarCollapsed ? 'lg:w-16' : 'w-64' }} bg-surface border-r border-border transform -translate-x-full lg:translate-x-0 lg:static lg:block transition-all duration-200 ease-in-out flex flex-col" aria-label="Navigasi Bilah Sisi">
        <div class="h-16 flex items-center justify-between px-4 border-b border-border shrink-0">
            @if($sidebarCollapsed)
                <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="h-8 w-auto mx-auto hidden lg:block object-contain">
                <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="h-8 w-auto lg:hidden object-contain">
            @else
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="h-8 w-auto object-contain">
                    <span class="font-bold text-lg text-primary">Admin</span>
                </div>
            @endif
            <button class="lg:hidden text-muted hover:text-slate-800" aria-label="Tutup bilah sisi">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <nav class="p-2 lg:p-4 overflow-y-auto flex-1">
            {{ $navigation ?? '' }}
            @yield('navigation')
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <header class="h-16 bg-surface border-b border-border flex items-center px-4 sm:px-6 sticky top-0 z-30 shrink-0">
            <div class="flex-1 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button class="lg:hidden p-2 -ml-2 text-muted hover:text-slate-800    rounded" aria-label="Buka bilah sisi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    @if(isset($breadcrumbs) || View::hasSection('breadcrumbs'))
                        <nav aria-label="Penunjuk arah" class="hidden sm:block">
                            {{ $breadcrumbs ?? '' }}
                            @yield('breadcrumbs')
                        </nav>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    {{ $toolbar ?? '' }}
                    @yield('toolbar')
                    {{ $actions ?? '' }}
                    @yield('actions')
                </div>
            </div>
        </header>

        <main id="main-content" class="flex-1 flex flex-col lg:flex-row overflow-hidden">
            <section class="flex-1 {{ $leftWidth }} overflow-y-auto p-4 sm:p-6 lg:p-8" aria-label="Informasi Detail">
                {{ $slot ?? '' }}
                @yield('content')
            </section>
            
            <aside class="w-full {{ $rightWidth }} bg-surface border-t lg:border-t-0 lg:border-l border-border overflow-y-auto p-4 sm:p-6 shadow-sm shrink-0" aria-label="Panel Aksi">
                {{ $aside ?? '' }}
                @yield('aside')
            </aside>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
