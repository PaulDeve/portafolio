<?php
/**
 * Página principal del sistema
 * ComercioSys - Sistema de Gestión de Ventas
 */

require_once 'includes/auth.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Si no está logueado, redirigir al login
header('Location: login.php');
exit();
?>
