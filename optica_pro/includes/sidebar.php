<div class="sidebar p-3">
    <h4 class="text-center mb-4" style="color: var(--text-dark);">
        <i class="fas fa-eye"></i> OpticaPro
    </h4>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
               href="/optica/optica_pro/index.php">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        
        <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'vendedor'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'active' : ''; ?>" 
               href="/optica/optica_pro/modules/clientes.php">
                <i class="fas fa-users me-2"></i> Clientes
            </a>
        </li>
        <?php endif; ?>
        
        <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'vendedor'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'productos.php' ? 'active' : ''; ?>" 
               href="/optica/optica_pro/modules/productos.php">
                <i class="fas fa-glasses me-2"></i> Productos
            </a>
        </li>
        <?php endif; ?>
        
        <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'vendedor'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ventas.php' ? 'active' : ''; ?>" 
               href="/optica/optica_pro/modules/ventas.php">
                <i class="fas fa-shopping-cart me-2"></i> Ventas
            </a>
        </li>
        <?php endif; ?>
        
        <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'optometrista'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'citas.php' ? 'active' : ''; ?>" 
               href="/optica/optica_pro/modules/citas.php">
                <i class="fas fa-calendar-check me-2"></i> Citas
            </a>
        </li>
        <?php endif; ?>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reportes.php' ? 'active' : ''; ?>" 
               href="/optica/optica_pro/modules/reportes.php">
                <i class="fas fa-chart-bar me-2"></i> Reportes
            </a>
        </li>
        
        <?php if ($_SESSION['rol'] == 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>" 
               href="/optica/optica_pro/modules/usuarios.php">
                <i class="fas fa-user-cog me-2"></i> Usuarios
            </a>
        </li>
        <?php endif; ?>
        
    </ul>

    <!-- Logout fijo en la parte inferior izquierda del sidebar -->
    <div class="logout-fixed">
        <a href="/optica/optica_pro/auth/logout.php" class="btn btn-logout">
            <i class="fas fa-sign-out-alt me-2"></i>
            Cerrar Sesión
        </a>
    </div>
</div>