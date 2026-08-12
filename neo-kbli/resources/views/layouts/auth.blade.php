@props(['title', 'description', 'footer'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' . config('app.name', 'Neo KBLI') : config('app.name', 'Neo KBLI') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @include('components.theme-script')
</head>
<body class="font-sans text-slate-800 bg-background antialiased min-h-screen flex items-center justify-center p-4 sm:p-8 relative">
    <!-- Theme Toggle -->
    <div class="absolute top-4 right-4 sm:top-8 sm:right-8 z-50">
        <x-ui.theme-toggle />
    </div>

    <main class="w-full max-w-md bg-surface p-6 sm:p-8 rounded-lg border border-border shadow-sm">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 rounded transition-shadow">
                <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="h-12 md:h-14 w-auto mx-auto object-contain drop-shadow-sm hover:drop-shadow transition-all">
            </a>
            @if(isset($description))
                <p class="text-sm text-muted mt-2">{{ $description }}</p>
            @endif
        </div>
        
        {{ $slot ?? '' }}
        @yield('content')
        
        @if(isset($footer))
            <footer class="mt-6 text-center">
                {{ $footer }}
            </footer>
        @elseif(View::hasSection('footer'))
            <footer class="mt-6 text-center">
                @yield('footer')
            </footer>
        @endif
    </main>

    @stack('scripts')
</body>
</html>
