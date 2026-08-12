@extends('layouts.admin')

@section('title', 'Kategori Utama KBLI')

@section('content')
<div class="bg-white rounded-md border border-gray-200 p-6">
    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Kategori Utama</h3>
            <p class="text-sm text-gray-500 mt-0.5">Daftar tingkat teratas klasifikasi KBLI.</p>
        </div>

        <form action="{{ route('admin.kbli.categories') }}" method="GET" class="w-full md:w-auto">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm    "
                    placeholder="Cari kode atau nama...">
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 border border-primary-600">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto border border-gray-200 rounded-md">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Kode</th>
                    <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kategori</th>
                    <th scope="col" class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 whitespace-nowrap font-mono font-semibold text-gray-900">
                        {{ $category->code }}
                    </td>
                    <td class="px-5 py-3 text-gray-800">
                        {{ $category->name }}
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-center">
                        <a href="{{ route('admin.kbli.sections', ['category_id' => $category->id]) }}"
                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Lihat Seksi
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-5 py-10 text-center text-sm text-gray-400">
                        Tidak ada kategori KBLI yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $categories->withQueryString()->links() }}
    </div>
</div>
@endsection
