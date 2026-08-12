@props(['title'])

<li class="mt-6">
    <div x-show="!collapsed" class="text-xs font-semibold leading-6 text-slate-400 uppercase tracking-wider px-2 whitespace-nowrap">{{ $title }}</div>
    <div x-show="collapsed" class="my-2 border-t border-slate-200/60"></div>
    <ul role="list" class="mt-1 space-y-1">
        {{ $slot }}
    </ul>
</li>
