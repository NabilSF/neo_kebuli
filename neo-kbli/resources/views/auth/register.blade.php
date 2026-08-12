<x-layouts.auth 
    title="Daftar Akun Baru" 
    description="Lengkapi formulir di bawah ini untuk mendaftar"
>
    <!-- Back Button -->
    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-900 mb-6 transition-colors group">
        <svg class="w-4 h-4 mr-1.5 text-slate-400 group-hover:text-slate-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Beranda
    </a>

    @if ($errors->any())
        <x-feedback.alert variant="error" title="Gagal Mendaftar" class="mb-4">
            Periksa kembali isian formulir Anda.
        </x-feedback.alert>
    @endif

    <form method="POST" action="{{ route('register.post') }}" class="flex flex-col gap-5">
        @csrf
        
        <x-forms.field label="Nama Lengkap" name="name" required>
            <x-forms.input id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="Masukkan nama lengkap Anda" />
        </x-forms.field>

        <x-forms.field label="Alamat Email" name="email" required>
            <x-forms.input id="email" type="email" name="email" :value="old('email')" required placeholder="user@example.com" />
        </x-forms.field>

        <x-forms.field label="Kata Sandi" name="password" required>
            <x-forms.input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
        </x-forms.field>

        <x-forms.field label="Konfirmasi Kata Sandi" name="password_confirmation" required>
            <x-forms.input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        </x-forms.field>

        <div class="flex items-start" x-data="{ termsOpen: false }">
            <div class="flex items-center h-5">
                <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 rounded border-border text-primary ">
            </div>
            <div class="ml-2 text-sm">
                <label for="terms" class="font-medium text-slate-700">Saya menyetujui <button type="button" @click="termsOpen = true" class="text-primary hover:underline">Syarat dan Ketentuan</button> serta Kebijakan Privasi yang berlaku</label>
            </div>
            
            <!-- Terms Modal -->
            <div x-show="termsOpen" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div x-show="termsOpen" x-transition.opacity class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity" aria-hidden="true"></div>

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
                             class="relative transform overflow-hidden rounded-lg bg-surface text-left border border-border transition-all sm:my-8 sm:w-full sm:max-w-lg shadow-xl">
                            <div class="bg-surface px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <h3 class="text-lg font-bold text-slate-900" id="modal-title">Syarat dan Ketentuan</h3>
                                <div class="mt-4 space-y-3 text-sm text-slate-600">
                                    <p>1. Penggunaan Data: Data pribadi yang Anda masukkan akan digunakan secara eksklusif untuk keperluan sistem Neo KBLI dan tidak akan dibagikan ke pihak ketiga tanpa persetujuan Anda.</p>
                                    <p>2. Kewajiban Pengguna: Anda berkewajiban memberikan data yang valid dan bertanggung jawab penuh atas segala aktivitas di dalam akun Anda.</p>
                                    <p>3. Hak Cipta: Seluruh konten dan struktur KBLI mengacu pada peraturan pemerintah yang berlaku.</p>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-border">
                                <x-ui.button type="button" @click="termsOpen = false" variant="primary">Tutup</x-ui.button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-forms.actions class="mt-4">
            <x-slot name="primary">
                <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                    Daftar Akun
                </x-ui.button>
            </x-slot>
        </x-forms.actions>
    </form>
    
    <x-slot name="footer">
        <p class="text-sm text-slate-600">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Masuk</a></p>
    </x-slot>
</x-layouts.auth>
