<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header 
            title="Ubah KBLI" 
            description="Ubah entri data KBLI ({{ $item->code }})."
        />
    </x-slot>

    <div class="max-w-3xl">
        <form action="{{ route('admin.kbli.items.update', $item) }}" method="POST">
            @csrf
            @method('PUT')
            
            <x-forms.section title="Informasi Utama" description="Masukkan kode dan nama kegiatan ekonomi.">
                <x-ui.card class="flex flex-col gap-6">
                    <x-forms.field label="Kode KBLI" name="code" required>
                        <x-forms.input type="text" name="code" :value="old('code', $item->code)" required />
                    </x-forms.field>

                    <x-forms.field label="Nama Lapangan Usaha" name="name" required>
                        <x-forms.input type="text" name="name" :value="old('name', $item->name)" required />
                    </x-forms.field>

                    <x-forms.field label="Deskripsi" name="description">
                        <x-forms.textarea name="description" rows="4">{{ old('description', $item->description) }}</x-forms.textarea>
                    </x-forms.field>
                </x-ui.card>
            </x-forms.section>

            <x-forms.section title="Klasifikasi & Status" description="Tentukan golongan pokok dan status keaktifan." class="mt-8">
                <x-ui.card class="flex flex-col gap-6">
                    <x-forms.field label="Golongan Pokok" name="section_id" required>
                        <select name="section_id" class="w-full rounded-md border-border   text-sm min-h-[44px]">
                            <option value="">Pilih Golongan Pokok...</option>
                            @foreach($sections as $id => $name)
                                <option value="{{ $id }}" {{ old('section_id', $item->section_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </x-forms.field>

                    <x-forms.field label="Status Keaktifan" name="is_active">
                        <x-forms.switch name="is_active" id="is_active" :checked="old('is_active', $item->is_active) ? true : false">
                            Aktif (Bisa digunakan dalam pengajuan)
                        </x-forms.switch>
                    </x-forms.field>
                </x-ui.card>
            </x-forms.section>

            <x-forms.actions class="mt-8">
                <x-slot name="secondary">
                    <x-ui.button type="button" variant="outline" onclick="window.location='{{ route('admin.kbli.items.index') }}'">Batal</x-ui.button>
                </x-slot>
                <x-slot name="primary">
                    <x-ui.button type="submit" variant="primary">Simpan Perubahan</x-ui.button>
                </x-slot>
            </x-forms.actions>
        </form>
    </div>
</x-layouts.admin>
