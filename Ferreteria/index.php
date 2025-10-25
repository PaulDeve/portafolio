<?php
// index.php - Punto de entrada simple
require_once __DIR__ . '/config/conexion.php';

// Si no hay sesión iniciada, redirigir a login
if (!isset($_SESSION['usuario'])) {
    header('Location: views/login.php');
    exit;
}

// Redirigir al dashboard
header('Location: views/dashboard.php');
exit;
?>
