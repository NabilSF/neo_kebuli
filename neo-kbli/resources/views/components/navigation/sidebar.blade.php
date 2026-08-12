<div class="flex h-full flex-col gap-y-5 overflow-y-auto px-4 py-4">
    <!-- Logo & Toggle Region -->
    <div class="flex h-12 shrink-0 items-center justify-between px-2">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-primary overflow-hidden outline-none focus-visible:ring-2 focus-visible:ring-primary rounded">
            <img src="{{ asset('images/logo.png') }}" alt="Neo KBLI" class="w-8 h-8 object-contain shrink-0">
            <span x-show="!collapsed" class="tracking-tight whitespace-nowrap">Neo KBLI</span>
        </a>
        
        <!-- Toggle Button for Desktop -->
        <button type="button" 
                @click="toggleSidebar()" 
                class="hidden lg:flex p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                :title="collapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'">
            <svg class="w-5 h-5 transform transition-transform" :class="collapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </button>
    </div>

    <!-- Navigation List -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-6">
            <li>
                <ul role="list" class="-mx-1 space-y-1">
                    <x-navigation.sidebar-item href="{{ route('home') }}" icon="home">
                        Beranda
                    </x-navigation.sidebar-item>
                    
                    @can('dashboard.view')
                    <x-navigation.sidebar-item href="{{ route('admin.dashboard') ?? '#' }}" active="{{ request()->routeIs('admin.dashboard') }}" icon="layout-dashboard">
                        Dashboard
                    </x-navigation.sidebar-item>
                    @endcan
                    
                    @can('application.view')
                    <x-navigation.sidebar-group title="Pengajuan">
                        <x-navigation.sidebar-item href="{{ route('admin.applications.index') }}" active="{{ request()->routeIs('admin.applications.*') }}" icon="file-text">Daftar Pengajuan</x-navigation.sidebar-item>
                    </x-navigation.sidebar-group>
                    @endcan

                    <x-navigation.sidebar-group title="Katalog KBLI">
                        <x-navigation.sidebar-item href="{{ route('admin.kbli.items.index') }}" active="{{ request()->routeIs('admin.kbli.items.index') }}" icon="library-big">Daftar KBLI</x-navigation.sidebar-item>
                        <x-navigation.sidebar-item href="{{ route('admin.kbli.items.create') }}" active="{{ request()->routeIs('admin.kbli.items.create') }}" icon="plus-circle">Tambah KBLI</x-navigation.sidebar-item>
                        <x-navigation.sidebar-item href="{{ route('admin.kbli.import.form') }}" active="{{ request()->routeIs('admin.kbli.import.*') }}" icon="upload">Import KBLI</x-navigation.sidebar-item>
                        <x-navigation.sidebar-item href="{{ route('admin.kbli.export') }}" icon="download">Export KBLI</x-navigation.sidebar-item>
                        <x-navigation.sidebar-item href="{{ route('admin.kbli.import.template', ['format' => 'xlsx']) }}" icon="file-text">Template Excel</x-navigation.sidebar-item>
                    </x-navigation.sidebar-group>

                    <x-navigation.sidebar-group title="Manajemen Data">
                        @hasanyrole('Super Admin|super-admin|Administrator|admin')
                        <x-navigation.sidebar-item href="{{ route('admin.users.index') }}" active="{{ request()->routeIs('admin.users.*') }}" icon="users">Pengguna</x-navigation.sidebar-item>
                        @endhasanyrole
                        <x-navigation.sidebar-item href="{{ route('admin.news.index') }}" active="{{ request()->routeIs('admin.news.*') }}" icon="newspaper">Berita</x-navigation.sidebar-item>
                        <x-navigation.sidebar-item href="{{ route('user.notifications.index') }}" active="{{ request()->routeIs('user.notifications.*') }}" icon="bell">Notifikasi</x-navigation.sidebar-item>
                    </x-navigation.sidebar-group>
                </ul>
            </li>
            
            <li class="mt-auto">
                <ul role="list" class="-mx-1 space-y-1">
                    <x-navigation.sidebar-item href="{{ route('admin.settings.index') }}" active="{{ request()->routeIs('admin.settings.*') }}" icon="settings">
                        Pengaturan
                    </x-navigation.sidebar-item>
                </ul>
            </li>
        </ul>
    </nav>
</div>
