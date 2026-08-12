<x-layouts.admin>
    <x-slot name="header">
        <x-workspace.header 
            title="Preview Import KBLI" 
            description="Tinjau data yang akan diimpor. Data dengan error (status ERROR) tidak akan dimasukkan ke database."
        />
        <div class="mt-4 flex gap-4">
            <a href="{{ route('admin.kbli.import.form') }}" class="text-sm text-gray-500 hover:text-gray-700">
                &larr; Batal & Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl">
        <div class="grid grid-cols-4 gap-4 mb-6">
            <x-ui.card class="p-4 flex flex-col justify-center items-center">
                <div class="text-sm text-gray-500">Total Rows</div>
                <div class="text-2xl font-bold">{{ $preview['total_rows'] ?? 0 }}</div>
            </x-ui.card>
            <x-ui.card class="p-4 flex flex-col justify-center items-center bg-green-50">
                <div class="text-sm text-green-600">Valid Rows</div>
                <div class="text-2xl font-bold text-green-700">{{ $preview['valid_rows'] ?? 0 }}</div>
            </x-ui.card>
            <x-ui.card class="p-4 flex flex-col justify-center items-center bg-red-50">
                <div class="text-sm text-red-600">Invalid Rows</div>
                <div class="text-2xl font-bold text-red-700">{{ $preview['invalid_rows'] ?? 0 }}</div>
            </x-ui.card>
        </div>

        <div class="mb-4 p-4 bg-yellow-50 text-yellow-800 rounded-md text-sm">
            <strong>Warning:</strong> Existing KBLI code will NOT be overwritten. Duplicate codes will be marked as ERROR and rejected.
        </div>

        <x-ui.card class="mb-8 overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Row</th>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Section ID</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($preview['rows'] ?? [] as $row)
                        <tr class="border-b {{ $row['status'] === 'ERROR' ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3">{{ $row['row_num'] }}</td>
                            <td class="px-4 py-3">{{ $row['code'] }}</td>
                            <td class="px-4 py-3">{{ $row['name'] }}</td>
                            <td class="px-4 py-3">{{ $row['section_id'] }}</td>
                            <td class="px-4 py-3 font-semibold {{ $row['status'] === 'ERROR' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $row['status'] }}
                            </td>
                            <td class="px-4 py-3 text-red-500 text-xs">{{ $row['reason'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center">Tidak ada data ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.card>

        @if(($preview['valid_rows'] ?? 0) > 0)
            <form action="{{ route('admin.kbli.import.confirm') }}" method="POST">
                @csrf
                <input type="hidden" name="cache_key" value="{{ $cacheKey }}">
                <x-forms.actions>
                    <x-slot name="primary">
                        <x-ui.button type="submit" variant="primary">Confirm Import ({{ $preview['valid_rows'] }} valid rows)</x-ui.button>
                    </x-slot>
                </x-forms.actions>
            </form>
        @else
            <x-forms.actions>
                <x-slot name="primary">
                    <x-ui.button type="button" variant="primary" disabled>Tidak ada baris valid</x-ui.button>
                </x-slot>
            </x-forms.actions>
        @endif
    </div>
</x-layouts.admin>
