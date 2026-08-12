@props(['href', 'active' => false, 'icon' => null, 'badge' => null])

@php
    $baseClasses = 'group flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 transition-colors relative';
    $activeClasses = $active 
        ? 'bg-primary/5 text-primary' 
        : 'text-slate-700 hover:text-primary hover:bg-slate-50';
        
    $iconActiveClasses = $active 
        ? 'text-primary' 
        : 'text-slate-400 group-hover:text-primary';
@endphp

<li>
    <a href="{{ $href }}" 
       :title="collapsed ? '{{ $slot }}' : ''"
       {{ $attributes->merge(['class' => $baseClasses . ' ' . $activeClasses]) }}
       :class="collapsed ? 'justify-center px-2' : ''"
       @if($active) aria-current="page" @endif>
        @if($icon)
            <span class="inline-flex shrink-0 {{ $iconActiveClasses }}" aria-hidden="true">
                @switch($icon)
                    @case('dashboard')
                    @case('layout-dashboard')
                        <!-- Lucide LayoutDashboard -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <rect width="7" height="9" x="3" y="3" rx="1"/>
                            <rect width="7" height="5" x="14" y="3" rx="1"/>
                            <rect width="7" height="9" x="14" y="12" rx="1"/>
                            <rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                        @break
                    @case('file-text')
                    @case('document')
                        <!-- Lucide FileText -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v4a2 2 0 0 0 2 2h4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 9H8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 13H8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 17H8"/>
                        </svg>
                        @break
                    @case('library-big')
                    @case('database')
                        <!-- Lucide LibraryBig -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <rect width="8" height="18" x="3" y="3" rx="1"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.4 18.9c.2.5-.1 1.1-.6 1.3l-1.9.7c-.5.2-1.1-.1-1.3-.6L11.1 5.1c-.2-.5.1-1.1.6-1.3l1.9-.7c.5-.2 1.1.1 1.3.6Z"/>
                        </svg>
                        @break
                    @case('users')
                        <!-- Lucide Users -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        @break
                    @case('settings')
                        <!-- Lucide Settings -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        @break
                    @default
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <rect width="7" height="9" x="3" y="3" rx="1"/>
                            <rect width="7" height="5" x="14" y="3" rx="1"/>
                            <rect width="7" height="9" x="14" y="12" rx="1"/>
                            <rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                @endswitch
            </span>
        @endif
        <span x-show="!collapsed" class="truncate whitespace-nowrap">{{ $slot }}</span>
        @if($badge)
            <span x-show="!collapsed" class="ml-auto w-9 min-w-max whitespace-nowrap rounded-full bg-surface px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-slate-600" aria-hidden="true">{{ $badge }}</span>
        @endif
    </a>
</li>
