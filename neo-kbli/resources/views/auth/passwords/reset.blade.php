<x-layouts.auth 
    title="Atur Ulang Kata Sandi" 
    description="Silakan masukkan kata sandi baru Anda."
>
    @if ($errors->any())
        <x-feedback.alert variant="error" title="Gagal" class="mb-4">
            Periksa kembali isian formulir Anda.
        </x-feedback.alert>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">

        <x-forms.field label="Alamat Email" name="email" required>
            <x-forms.input id="email" type="email" name="email" :value="$email ?? old('email')" required autofocus autocomplete="username" readonly class="bg-slate-50 text-slate-500" />
        </x-forms.field>

        <x-forms.field label="Kata Sandi Baru" name="password" required>
            <x-forms.input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
        </x-forms.field>

        <x-forms.field label="Konfirmasi Kata Sandi" name="password_confirmation" required>
            <x-forms.input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        </x-forms.field>

        <x-forms.actions class="mt-4">
            <x-slot name="primary">
                <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                    Simpan Kata Sandi
                </x-ui.button>
            </x-slot>
        </x-forms.actions>
    </form>
</x-layouts.auth>
