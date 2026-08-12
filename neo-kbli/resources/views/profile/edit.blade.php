<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header title="Profil Pengguna" description="Kelola informasi pribadi dan keamanan akun Anda." />
    </x-slot>

    <div class="flex flex-col gap-8 max-w-4xl mx-auto">
        
        <!-- Informasi Dasar -->
        <x-forms.section title="Informasi Dasar" description="Perbarui informasi profil dan alamat surel Anda.">
            <x-ui.card>
                <form method="post" action="{{ route('profile.update') ?? '#' }}" class="flex flex-col gap-6">
                    @csrf
                    @method('patch')

                    <div class="flex items-center gap-6">
                        <x-feedback.skeleton type="avatar" class="h-20 w-20" />
                        <div>
                            <x-ui.button type="button" variant="outline" size="sm">Ubah Avatar</x-ui.button>
                            <p class="text-xs text-muted mt-2">JPG, GIF atau PNG maksimal 2MB.</p>
                        </div>
                    </div>

                    <x-forms.field label="Nama Lengkap" name="name" required>
                        <x-forms.input id="name" type="text" name="name" :value="old('name', auth()->user()->name ?? '')" required autofocus autocomplete="name" />
                    </x-forms.field>

                    <x-forms.field label="Alamat Email" name="email" required>
                        <x-forms.input id="email" type="email" name="email" :value="old('email', auth()->user()->email ?? '')" required autocomplete="username" />
                    </x-forms.field>
                    
                    <x-forms.actions class="mt-2 border-t border-border pt-4">
                        <x-slot name="primary">
                            <x-ui.button type="submit" variant="primary">Simpan Perubahan</x-ui.button>
                        </x-slot>
                    </x-forms.actions>
                </form>
            </x-ui.card>
        </x-forms.section>

        <!-- Ubah Kata Sandi -->
        <x-forms.section title="Ubah Kata Sandi" description="Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.">
            <x-ui.card>
                <form method="post" action="{{ route('password.update') ?? '#' }}" class="flex flex-col gap-6">
                    @csrf
                    @method('put')

                    <x-forms.field label="Kata Sandi Saat Ini" name="current_password" required>
                        <x-forms.input id="current_password" type="password" name="current_password" autocomplete="current-password" />
                    </x-forms.field>

                    <x-forms.field label="Kata Sandi Baru" name="password" required>
                        <x-forms.input id="password" type="password" name="password" autocomplete="new-password" />
                    </x-forms.field>

                    <x-forms.field label="Konfirmasi Kata Sandi" name="password_confirmation" required>
                        <x-forms.input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" />
                    </x-forms.field>

                    <x-forms.actions class="mt-2 border-t border-border pt-4">
                        <x-slot name="primary">
                            <x-ui.button type="submit" variant="primary">Ubah Kata Sandi</x-ui.button>
                        </x-slot>
                    </x-forms.actions>
                </form>
            </x-ui.card>
        </x-forms.section>

    </div>
</x-layouts.admin>
