<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ferretería - Sistema</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papNM6VY6o3p3KqZk3QqX5g0q9qYQ1Vn3YQfZ6R6a1Q6m0j8hF3KcY9jVQeXw5q3g6Q/0a1K9Y4v3aQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/Ferreteria/assets/css/style.css">
  </head>
  <body>
    <?php
    $currentScript = basename($_SERVER['SCRIPT_NAME']);
    $showSidebar = isset($_SESSION['usuario']) && $currentScript !== 'login.php';
    ?>
    <div class="d-flex" id="appLayout">
      <?php if ($showSidebar): ?>
      <nav id="sidebar" class="bg-dark text-light">
        <div class="sidebar-header p-3 text-center border-bottom">
          <a href="/Ferreteria/views/dashboard.php" class="text-decoration-none text-light"><strong>Ferretería</strong></a>
        </div>
        <div class="sidebar-body p-2">
          <ul class="nav flex-column">
            <?php if(isset($_SESSION['usuario'])): ?>
              <?php require_once __DIR__ . '/../config/roles.php'; ?>
              <?php if (can_manage_products() || is_admin()): ?>
                <li class="nav-item"><a class="nav-link text-light" href="/Ferreteria/views/productos.php"><i class="fa fa-box me-2"></i>Productos</a></li>
              <?php endif; ?>
              <li class="nav-item"><a class="nav-link text-light" href="/Ferreteria/views/clientes.php"><i class="fa fa-user-friends me-2"></i>Clientes</a></li>
              <?php if (can_sell()): ?>
                <li class="nav-item"><a class="nav-link text-light" href="/Ferreteria/views/ventas.php"><i class="fa fa-cash-register me-2"></i>Ventas</a></li>
              <?php endif; ?>
              <?php if (is_recepcion() || is_admin()): ?>
                <li class="nav-item"><a class="nav-link text-light" href="/Ferreteria/views/proveedores.php"><i class="fa fa-truck me-2"></i>Proveedores</a></li>
              <?php endif; ?>
              <?php if (is_admin()): ?>
                <li class="nav-item"><a class="nav-link text-light" href="/Ferreteria/views/usuarios.php"><i class="fa fa-users-cog me-2"></i>Usuarios</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="/Ferreteria/views/reportes.php"><i class="fa fa-chart-line me-2"></i>Reportes</a></li>
              <?php endif; ?>
              <li class="nav-item mt-3"><a class="nav-link text-light" href="/Ferreteria/views/logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a></li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link text-light" href="/Ferreteria/views/login.php"><i class="fa fa-sign-in-alt me-2"></i>Login</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </nav>
      <?php endif; ?>
      <main class="flex-fill p-4" style="padding-left:<?= $showSidebar ? '0' : '0' ?>;">
        <div class="app-container" style="max-width:900px; margin: 0 auto; <?= $showSidebar ? '' : '' ?>">
          <!-- Toggle button visible on mobile to open sidebar -->
          <button id="btnToggleSidebar" class="btn btn-outline-secondary d-md-none mb-3"><i class="fa fa-bars"></i></button>
