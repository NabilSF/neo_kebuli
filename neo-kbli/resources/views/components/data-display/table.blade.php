@props([
    'loading' => false,
    'empty' => false,
    'error' => false,
    'errorMessage' => 'Data gagal dimuat.',
    'emptyMessage' => 'Belum ada data.',
])

<div class="w-full flex flex-col bg-surface border border-border rounded-lg shadow-sm overflow-hidden">
    @if(isset($toolbar))
        <div class="border-b border-border bg-slate-50/50">
            {{ $toolbar }}
        </div>
    @endif
    
    <div class="overflow-x-auto w-full">
        @if($loading)
            <div class="p-8 flex justify-center">
                <x-feedback.spinner size="lg" srText="Memuat tabel..." />
            </div>
        @elseif($error)
            <div class="p-8">
                <x-feedback.alert variant="error" title="Terjadi Kesalahan" :description="$errorMessage" />
            </div>
        @elseif($empty)
            <div class="p-12">
                <x-feedback.empty-state :title="$emptyMessage" />
            </div>
        @else
            <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse min-w-full divide-y divide-border']) }}>
                @if(isset($head))
                    <thead class="bg-slate-50 border-b border-border">
                        <tr>
                            {{ $head }}
                        </tr>
                    </thead>
                @endif
                <tbody class="divide-y divide-border bg-surface">
                    {{ $slot }}
                </tbody>
            </table>
        @endif
    </div>
    
    @if(isset($pagination))
        <div class="border-t border-border bg-slate-50 px-4 py-3 sm:px-6">
            {{ $pagination }}
        </div>
    @endif
</div>
