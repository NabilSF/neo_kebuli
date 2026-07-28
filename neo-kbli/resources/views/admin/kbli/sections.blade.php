@extends('layouts.admin')

@section('title', 'Seksi Klasifikasi KBLI')

@section('content')
<div class="bg-white rounded-md border border-gray-200 p-6">
    <!-- Header & Filters -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Seksi Klasifikasi</h3>
            <p class="text-sm text-gray-500 mt-0.5">Daftar tingkat kedua klasifikasi KBLI.</p>
        </div>

        <form action="{{ route('admin.kbli.sections') }}" method="GET" class="w-full md:w-auto flex flex-col md:flex-row gap-2">
            <select name="category_id"
                class="block px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 bg-white"
                onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->code }} - {{ Str::limit($category->name, 40) }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Cari seksi...">
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 border border-primary-600 whitespace-nowrap">
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
                    <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Kode</th>
                    <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Kategori</th>
                    <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Seksi</th>
                    <th scope="col" class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($sections as $section)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 whitespace-nowrap font-mono font-semibold text-gray-900">
                        {{ $section->code }}
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($section->category)
                            <span class="font-mono text-xs text-gray-600 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded">{{ $section->category->code }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-800">
                        {{ $section->name }}
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-center">
                        <a href="{{ route('admin.kbli.items', ['section_id' => $section->id]) }}"
                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Lihat Item
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-400">
                        Tidak ada seksi KBLI yang ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $sections->withQueryString()->links() }}
    </div>
</div>
@endsection
