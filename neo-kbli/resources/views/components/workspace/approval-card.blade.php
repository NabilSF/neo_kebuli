@props([
    'title' => 'Persetujuan Dokumen',
    'description' => 'Tinjau dan berikan keputusan terhadap pengajuan ini.',
    'actions' => null,
])

<x-ui.card>
    <div class="sm:flex sm:items-start justify-between">
        <div>
            <h3 class="text-base font-semibold leading-6 text-slate-900">
                {{ $title }}
            </h3>
            <div class="mt-2 max-w-xl text-sm text-slate-500">
                <p>{{ $description }}</p>
            </div>
        </div>
        <div class="mt-5 sm:ml-6 sm:mt-0 sm:flex sm:flex-shrink-0 sm:items-center gap-3">
            {{ $actions ?? $slot }}
        </div>
    </div>
</x-ui.card>
