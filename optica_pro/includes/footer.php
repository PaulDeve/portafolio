<footer class="text-center text-muted py-3 mt-auto">
    <div class="container">
        <!-- copyright removed per user request -->
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="/optica/optica_pro/assets/js/main.js"></script>

<!-- Theme selector script -->

<!-- Theme toggle script -->
<script>
(function(){
    try {
        const stored = localStorage.getItem('optica_theme') || 'neumorphism';
        const isDark = stored === 'dark';
        if (isDark) document.documentElement.setAttribute('data-theme','dark'); else document.documentElement.removeAttribute('data-theme');
        const btn = document.getElementById('themeToggle');
        if (btn) {
            btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            btn.classList.toggle('is-dark', isDark);
            btn.innerHTML = isDark ? '<span class="theme-toggle-inner"><i class="fas fa-moon"></i></span>' : '<span class="theme-toggle-inner"><i class="fas fa-sun"></i></span>';
        }
    } catch(e){ console.warn('theme init', e); }
})();

// Attach handler immediately (footer is loaded after navbar so button exists)
(function(){
    const btn = document.getElementById('themeToggle');
    if(!btn) return;
    btn.addEventListener('click', function(){
        const currentlyPressed = this.getAttribute('aria-pressed') === 'true';
        const makeDark = !currentlyPressed;
        this.setAttribute('aria-pressed', makeDark ? 'true' : 'false');
        this.classList.toggle('is-dark', makeDark);
        if (makeDark) {
            document.documentElement.setAttribute('data-theme','dark');
            try { localStorage.setItem('optica_theme','dark'); } catch(e){}
            this.innerHTML = '<span class="theme-toggle-inner"><i class="fas fa-moon"></i></span>';
        } else {
            document.documentElement.removeAttribute('data-theme');
            try { localStorage.setItem('optica_theme','neumorphism'); } catch(e){}
            this.innerHTML = '<span class="theme-toggle-inner"><i class="fas fa-sun"></i></span>';
        }
    });
})();
</script>

</body>
</html>