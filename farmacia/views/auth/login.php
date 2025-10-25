<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

$logoutMessage = isset($_GET['logout']) ? 'Has cerrado sesión exitosamente.' : null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Farmacia</title>
    <!-- Bootstrap 5 -->
    <link href="../../assets/vendor/bootstrap/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="../../assets/css/styles.css" rel="stylesheet">
    <!-- Google Fonts (Poppins) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">

    <div class="login-container">
        <div class="login-box">
            <div class="logo-container text-center mb-4">
                <img src="../../assets/img/logo.svg" alt="Logo Farmacia" class="login-logo">
            </div>
            <h3 class="text-center mb-4">Bienvenido</h3>

            <?php if ($errorMessage): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <?php if ($logoutMessage): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($logoutMessage) ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/AuthController.php?action=login" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                    <label for="username">Usuario</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                    <label for="password">Contraseña</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">Ingresar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../../assets/vendor/bootstrap/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
