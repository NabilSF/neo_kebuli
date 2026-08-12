<div x-data="{ theme: localStorage.getItem('neo-kbli-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') }" 
     @theme-changed.window="theme = $event.detail"
     class="inline-flex items-center bg-slate-100 dark:bg-neutral-800 rounded-full p-1 border border-slate-200 dark:border-neutral-700 shadow-sm"
     role="group" aria-label="Pilih tema">
    <button type="button" 
            @click="window.updateTheme('light'); theme = 'light'; window.dispatchEvent(new CustomEvent('theme-changed', {detail: 'light'}))"
            :class="theme === 'light' ? 'bg-white text-orange-500 shadow-sm dark:bg-neutral-700' : 'text-slate-500 hover:text-slate-700 dark:text-neutral-400 dark:hover:text-neutral-200'"
            class="p-1.5 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-orange-500"
            title="Terang" aria-label="Mode Terang">
        <!-- Lucide Sun Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
    </button>
    <button type="button" 
            @click="window.updateTheme('dark'); theme = 'dark'; window.dispatchEvent(new CustomEvent('theme-changed', {detail: 'dark'}))"
            :class="theme === 'dark' ? 'bg-white text-orange-500 shadow-sm dark:bg-neutral-700' : 'text-slate-500 hover:text-slate-700 dark:text-neutral-400 dark:hover:text-neutral-200'"
            class="p-1.5 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-orange-500"
            title="Gelap" aria-label="Mode Gelap">
        <!-- Lucide Moon Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
    </button>
</div>
