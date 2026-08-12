<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header 
            title="Import KBLI" 
            description="Unggah file Excel atau CSV untuk mengimpor data KBLI secara massal."
        />
        <div class="mt-4 flex gap-4">
            <a href="{{ route('admin.kbli.items.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Kembali ke Daftar KBLI
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="flex gap-4 mb-6">
            <a href="{{ route('admin.kbli.import.template', 'xlsx') }}">
                <x-ui.button type="button" variant="outline">Download Template XLSX</x-ui.button>
            </a>
            <a href="{{ route('admin.kbli.import.template', 'csv') }}">
                <x-ui.button type="button" variant="outline">Download Template CSV</x-ui.button>
            </a>
        </div>

        @if(session('error'))
            <x-feedback.alert variant="danger" class="mb-6">
                {{ session('error') }}
            </x-feedback.alert>
        @endif

        <form action="{{ route('admin.kbli.import.preview') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <x-forms.section title="Upload File" description="Pastikan file sesuai dengan format template yang disediakan.">
                <x-ui.card class="flex flex-col gap-6">
                    <x-forms.field label="File KBLI (XLSX, CSV, max 10MB)" name="file" required>
                        <input type="file" name="file" accept=".xlsx,.csv,.txt" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100" />
                    </x-forms.field>
                </x-ui.card>
            </x-forms.section>

            <x-forms.actions class="mt-8">
                <x-slot name="primary">
                    <x-ui.button type="submit" variant="primary">Preview Import</x-ui.button>
                </x-slot>
            </x-forms.actions>
        </form>
    </div>
</x-layouts.admin>
