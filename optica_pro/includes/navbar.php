<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <!-- Este espacio se deja para alinear el contenido a la derecha -->
        <div class="flex-grow-1"></div>
        
        <div class="ms-auto d-flex align-items-center">
            <!-- Theme toggle wrapped so background only covers the toggle area -->
            <div class="nav-theme-wrap me-3">
                <button id="themeToggle" class="theme-toggle" aria-pressed="false" title="Alternar tema" aria-label="Alternar tema">
                    <span class="theme-toggle-inner"><i class="fas fa-sun"></i></span>
                </button>
            </div>

            <span class="me-3">
                <i class="fas fa-user" style="color: var(--primary);"></i>
                <?php echo $_SESSION['nombre']; ?> 
                <span class="badge" style="background-color: var(--accent);"><?php echo ucfirst($_SESSION['rol']); ?></span>
            </span>
            <a href="/optica/optica_pro/auth/logout.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </div>
    </div>
</nav>