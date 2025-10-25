<?php
// El rol del usuario se obtiene del header.php, que debe ser incluido antes
$currentRole = strtolower($userRole ?? '');

// Define la página actual para el estado "active"
$currentPage = basename($_SERVER['PHP_SELF']);

?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none">
            <img src="<?= $baseUrl ?>/assets/img/logo.svg" alt="" width="32" height="32" class="me-2">
            <span class="fs-5 sidebar-text">Farmacia</span>
        </a>
    </div>

    <hr class="sidebar-divider">

    <ul class="nav nav-pills flex-column mb-auto">

        <?php // Menú del Administrador ?>
        <?php if ($currentRole === 'administrador'): ?>
            <li class="nav-item">
                <a href="<?= $baseUrl ?>/views/admin/dashboard.php" class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill me-2"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/views/admin/productos.php" class="nav-link <?= $currentPage == 'productos.php' ? 'active' : '' ?>">
                    <i class="bi bi-capsule-pill me-2"></i>
                    <span class="sidebar-text">Productos</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/views/admin/usuarios.php" class="nav-link <?= $currentPage == 'usuarios.php' ? 'active' : '' ?>">
                    <i class="bi bi-people-fill me-2"></i>
                    <span class="sidebar-text">Usuarios</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/views/admin/reportes.php" class="nav-link <?= $currentPage == 'reportes.php' ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-line-fill me-2"></i>
                    <span class="sidebar-text">Reportes</span>
                </a>
            </li>
             <li>
                <a href="<?= $baseUrl ?>/views/admin/configuracion.php" class="nav-link <?= $currentPage == 'configuracion.php' ? 'active' : '' ?>">
                    <i class="bi bi-gear-fill me-2"></i>
                    <span class="sidebar-text">Configuración</span>
                </a>
            </li>
        <?php endif; ?>

        <?php // Menú del Vendedor ?>
        <?php if ($currentRole === 'vendedor'): ?>
            <li class="nav-item">
                <a href="<?= $baseUrl ?>/views/vendedor/venta.php" class="nav-link <?= $currentPage == 'venta.php' ? 'active' : '' ?>">
                    <i class="bi bi-cart-plus-fill me-2"></i>
                    <span class="sidebar-text">Nueva Venta</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/views/vendedor/historial.php" class="nav-link <?= $currentPage == 'historial.php' ? 'active' : '' ?>">
                    <i class="bi bi-clock-history me-2"></i>
                    <span class="sidebar-text">Historial</span>
                </a>
            </li>
        <?php endif; ?>

        <?php // Menú del Recepcionista ?>
        <?php if ($currentRole === 'recepcionista'): ?>
            <li class="nav-item">
                <a href="<?= $baseUrl ?>/views/recepcionista/clientes.php" class="nav-link <?= $currentPage == 'clientes.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    <span class="sidebar-text">Clientes</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/views/recepcionista/citas.php" class="nav-link <?= $currentPage == 'citas.php' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-plus-fill me-2"></i>
                    <span class="sidebar-text">Pedidos</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/views/recepcionista/notificaciones.php" class="nav-link <?= $currentPage == 'notificaciones.php' ? 'active' : '' ?>">
                    <i class="bi bi-bell-fill me-2"></i>
                    <span class="sidebar-text">Notificaciones</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <hr class="sidebar-divider">

    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-2 fs-4"></i>
            <strong class="sidebar-text"><?= htmlspecialchars($userName) ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= $baseUrl ?>/controllers/AuthController.php?action=logout">Cerrar sesión</a></li>
        </ul>
    </div>
</div>

<div class="main-content" id="main-content">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <button class="btn btn-outline-light" id="sidebar-toggle"><i class="bi bi-list"></i></button>
            <div class="ms-auto">
                <span class="navbar-text">
                    Rol: <strong><?= htmlspecialchars($userRole) ?></strong>
                </span>
            </div>
        </div>
    </nav>
    <main class="p-4">
