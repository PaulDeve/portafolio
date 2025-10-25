<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Proteger la página. Si el usuario no está logueado, redirigir a login.
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/auth/login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Usuario';
$userRole = $_SESSION['user_role'] ?? 'Invitado';

// Definir la ruta base para evitar problemas con las rutas relativas
// Esto asume que la carpeta raíz del proyecto es /farmacia/
$baseUrl = '/farmacia'; 

?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Farmacia</title>

    <!-- Google Fonts (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="<?= $baseUrl ?>/assets/vendor/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos personalizados -->
    <link href="<?= $baseUrl ?>/assets/css/styles.css" rel="stylesheet">

</head>
<body class="dashboard-body">

    <div class="d-flex">
        <!-- Aquí se incluirá el sidebar -->
