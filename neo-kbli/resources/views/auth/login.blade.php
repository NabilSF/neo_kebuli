<x-layouts.auth 
    title="Masuk" 
    description="Silakan masuk menggunakan kredensial Anda untuk mengakses sistem."
>
    <!-- Back Button -->
    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-900 mb-6 transition-colors group">
        <svg class="w-4 h-4 mr-1.5 text-slate-400 group-hover:text-slate-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Beranda
    </a>

    <!-- Flash Messages via Alert (Feedback System) -->
    @if (session('status'))
        <x-feedback.alert variant="success" class="mb-4">
            {{ session('status') }}
        </x-feedback.alert>
    @endif
    
    @if ($errors->any())
        <x-feedback.alert variant="error" title="Gagal Masuk" class="mb-4">
            Periksa kembali alamat email dan kata sandi Anda.
        </x-feedback.alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
        @csrf

        <!-- Login Field (Email or Username) -->
        <x-forms.field label="Email atau Nama Pengguna" name="login" required>
            <x-forms.input id="login" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="admin@neokebuli.id atau username" />
        </x-forms.field>

        <!-- Password -->
        <x-forms.field label="Kata Sandi" name="password" required>
            <x-slot name="hint">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:underline    rounded">
                        Lupa Kata Sandi?
                    </a>
                @endif
            </x-slot>
            <x-forms.input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
        </x-forms.field>

        <!-- Remember Me -->
        <div class="mt-2">
            <x-forms.switch name="remember" id="remember" :checked="old('remember') ? true : false">
                Ingat saya di perangkat ini
            </x-forms.switch>
        </div>

        <x-forms.actions class="mt-4 flex flex-col gap-3">
            <x-slot name="primary">
                <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                    Masuk ke Sistem
                </x-ui.button>
            </x-slot>
        </x-forms.actions>
    </form>

    <x-slot name="footer">
        <p class="text-sm text-slate-600">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">
                Daftar sekarang
            </a>
        </p>
    </x-slot>
</x-layouts.auth>
