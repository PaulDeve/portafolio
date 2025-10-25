<?php
/**
 * Dashboard principal del sistema
 * ComercioSys - Sistema de Gestión de Ventas
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verificar que el usuario esté logueado
requireLogin();

$user = getCurrentUser();
$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾 ComercioSys - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>🧾 ComercioSys</h1>
            <div class="user-info">
                <span>Bienvenido, <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></span>
                <span class="user-role"><?php echo htmlspecialchars($user['rol']); ?></span>
                <a href="logout.php" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h2>Panel Principal</h2>
                <div class="datetime">
                    <i class="fas fa-calendar"></i>
                    <span id="current-datetime"></span>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_ventas']; ?></h3>
                        <p>Total de Ventas</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo formatCurrency($stats['monto_total']); ?></h3>
                        <p>Monto Total</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_usuarios']; ?></h3>
                        <p>Usuarios Registrados</p>
                    </div>
                </div>
            </div>

            <div class="modules-grid">
                <div class="module-card">
                    <div class="module-icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div class="module-content">
                        <h3>Gestión de Ventas</h3>
                        <p>Registrar, modificar y consultar ventas</p>
                        <a href="ventas.php" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i>
                            Acceder
                        </a>
                    </div>
                </div>

                <?php if (isAdmin()): ?>
                <div class="module-card">
                    <div class="module-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div class="module-content">
                        <h3>Gestión de Usuarios</h3>
                        <p>Administrar usuarios del sistema</p>
                        <a href="usuarios.php" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i>
                            Acceder
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <div class="module-card">
                    <div class="module-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="module-content">
                        <h3>Reportes</h3>
                        <p>Generar reportes de ventas</p>
                        <a href="ventas.php?action=reportes" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i>
                            Acceder
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2024 ComercioSys - Sistema de Gestión de Ventas</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
