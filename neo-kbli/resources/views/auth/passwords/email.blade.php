<x-layouts.auth 
    title="Lupa Kata Sandi" 
    description="Masukkan alamat email Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi."
>
    @if (session('status'))
        <x-feedback.alert variant="success" class="mb-4">
            {{ session('status') }}
        </x-feedback.alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
        @csrf

        <x-forms.field label="Alamat Email" name="email" required>
            <x-forms.input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="admin@neokebuli.id" />
        </x-forms.field>

        <x-forms.actions class="mt-4">
            <x-slot name="primary">
                <x-ui.button type="submit" variant="primary" class="w-full justify-center">
                    Kirim Tautan
                </x-ui.button>
            </x-slot>
            <x-slot name="secondary">
                <x-ui.button type="button" variant="outline" class="w-full justify-center" onclick="window.location='{{ route('login') }}'">
                    Kembali ke Log Masuk
                </x-ui.button>
            </x-slot>
        </x-forms.actions>
    </form>
</x-layouts.auth>
