<script>
    window.updateTheme = function(theme) {
        if (theme === 'dark') {
            localStorage.setItem('neo-kbli-theme', 'dark');
            document.documentElement.classList.add('dark');
        } else if (theme === 'light') {
            localStorage.setItem('neo-kbli-theme', 'light');
            document.documentElement.classList.remove('dark');
        } else {
            localStorage.removeItem('neo-kbli-theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    };
    
    // Initialize theme
    window.updateTheme(localStorage.getItem('neo-kbli-theme'));

    // Listen to OS theme changes if on system theme
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('neo-kbli-theme')) {
            window.updateTheme(null);
        }
    });
</script>
