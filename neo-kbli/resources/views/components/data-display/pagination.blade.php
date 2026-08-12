@props([
    'total' => 0,
    'from' => 0,
    'to' => 0,
    'paginator' => null, // Mendukung Laravel LengthAwarePaginator atau CursorPaginator
])

@php
    if ($paginator) {
        if (method_exists($paginator, 'total')) {
            $total = $paginator->total();
        } else {
            $total = 'banyak';
        }
        $from = method_exists($paginator, 'firstItem') ? $paginator->firstItem() : 0;
        $to = method_exists($paginator, 'lastItem') ? $paginator->lastItem() : 0;
    }
@endphp

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
    <div class="flex-1 flex items-center justify-between sm:hidden w-full">
        <x-ui.button variant="outline" size="sm" class="relative inline-flex items-center">
            Sebelumnya
        </x-ui.button>
        <x-ui.button variant="outline" size="sm" class="relative inline-flex items-center ml-3">
            Berikutnya
        </x-ui.button>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-700">
                Menampilkan
                <span class="font-medium">{{ $from }}</span>
                hingga
                <span class="font-medium">{{ $to }}</span>
                @if($total !== 'banyak')
                    dari
                    <span class="font-medium">{{ $total }}</span>
                @endif
                data
            </p>
        </div>
        <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Navigasi Halaman">
                @if($paginator && method_exists($paginator, 'links'))
                    {{ $paginator->links() }}
                @else
                    {{ $slot }}
                @endif
            </nav>
        </div>
    </div>
</div>
