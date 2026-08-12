@props([
    'action' => '#',
    'method' => 'POST',
    'placeholder' => 'Tambahkan catatan atau pesan revisi (opsional)...',
    'buttonText' => 'Kirim Catatan',
])

<div class="flex items-start space-x-4">
    <div class="flex-shrink-0">
        <img class="inline-block h-10 w-10 rounded-full bg-slate-50" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Guest') }}&background=random" alt="">
    </div>
    <div class="min-w-0 flex-1">
        <form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}">
            @if($method !== 'GET')
                @csrf
                @if(!in_array(strtoupper($method), ['GET', 'POST']))
                    @method($method)
                @endif
            @endif
            
            <div class="border-b border-border focus-within:border-primary">
                <label for="comment" class="sr-only">Tambah catatan</label>
                <textarea rows="3" name="comment" id="comment" class="block w-full resize-none border-0 border-b border-transparent p-0 pb-2 text-slate-900 placeholder:text-slate-400   sm:text-sm bg-transparent" placeholder="{{ $placeholder }}"></textarea>
            </div>
            
            <div class="flex justify-between pt-2">
                <div class="flex items-center space-x-5">
                    <!-- Placeholder for attach button if needed -->
                </div>
                <div class="flex-shrink-0">
                    <x-ui.button type="submit" variant="primary" size="sm">
                        {{ $buttonText }}
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>
</div>
