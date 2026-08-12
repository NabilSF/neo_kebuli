<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-border bg-surface px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 transition-all">
    <!-- Mobile Menu Trigger -->
    <button type="button" @click="mobileOpen = true" class="-m-2.5 p-2.5 text-slate-700 lg:hidden">
        <span class="sr-only">Buka bilah sisi</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
    </button>

    <div class="h-6 w-px bg-slate-200 lg:hidden" aria-hidden="true"></div>

    <div class="flex flex-1 items-center justify-between gap-x-4 self-stretch lg:gap-x-6">
        <!-- Direct Search Bar (Ctrl+K focuses input) -->
        <form action="{{ route('admin.kbli.items.index') }}" method="GET"
              class="relative flex items-center w-full max-w-full sm:w-[300px] md:w-[380px]"
              x-data
              @keydown.window.prevent.ctrl.k="if($refs.searchInput) { $refs.searchInput.focus(); $refs.searchInput.select(); }"
              @keydown.window.prevent.cmd.k="if($refs.searchInput) { $refs.searchInput.focus(); $refs.searchInput.select(); }">
            <label for="search-field" class="sr-only">Pencarian KBLI</label>
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </div>
            <input type="search"
                   name="search"
                   id="search-field"
                   x-ref="searchInput"
                   value="{{ request('search') }}"
                   @keydown.escape="$refs.searchInput.blur()"
                   placeholder="Pencarian KBLI..."
                   class="w-full border-0 py-2 pl-9 pr-14 text-slate-900 placeholder:text-slate-400 text-sm bg-slate-100/70 rounded-lg focus:bg-white focus:ring-1 focus:ring-slate-300 transition-colors">
            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none">
                <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded font-mono shadow-2xs">Ctrl K</kbd>
            </div>
        </form>
        
        <div class="flex items-center gap-x-4 lg:gap-x-6">
            <x-ui.theme-toggle />
            
            <a href="{{ route('user.notifications.index') }}" class="-m-2.5 p-2.5 text-slate-400 hover:text-slate-500 transition-colors inline-block">
                <span class="sr-only">Lihat notifikasi</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
            </a>

            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-slate-200 dark:bg-neutral-700" aria-hidden="true"></div>

            <!-- Admin Dropdown Menu -->
            <x-behavior.dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button type="button" class="-m-1.5 flex items-center p-1.5 rounded-md hover:bg-slate-100/60 transition-colors" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                        <span class="sr-only">Buka menu pengguna</span>
                        <img class="h-8 w-8 rounded-full bg-slate-50 border border-slate-200 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0D8ABC&color=fff" alt="{{ auth()->user()->name ?? 'Admin' }}">
                        <span class="hidden lg:flex lg:items-center">
                            <span class="ml-3 text-sm font-semibold leading-6 text-slate-900" aria-hidden="true">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <svg class="ml-2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </span>
                    </button>
                </x-slot>
                
                <a href="{{ route('user.profile.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-orange-500 transition-colors" role="menuitem" tabindex="-1">Profil Saya</a>
                <div class="border-t border-border my-1"></div>
                <form method="POST" action="{{ route('logout') }}" class="px-2 pb-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md transition-colors cursor-pointer flex items-center gap-2" role="menuitem" tabindex="-1">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </x-behavior.dropdown>
        </div>
    </div>
</div>
