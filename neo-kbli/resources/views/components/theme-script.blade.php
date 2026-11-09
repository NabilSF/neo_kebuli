<script>
    window.updateTheme = function(theme) {
        if (theme === 'dark') {
            localStorage.setItem('neo-kebuli-theme', 'dark');
            document.documentElement.classList.add('dark');
        } else if (theme === 'light') {
            localStorage.setItem('neo-kebuli-theme', 'light');
            document.documentElement.classList.remove('dark');
        } else {
            localStorage.removeItem('neo-kebuli-theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    };
    
    // Initialize theme
    window.updateTheme(localStorage.getItem('neo-kebuli-theme'));

    // Listen to OS theme changes if on system theme
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('neo-kebuli-theme')) {
            window.updateTheme(null);
        }
    });
</script>
