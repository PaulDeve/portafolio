<?php
/**
 * Página de login del sistema
 * ComercioSys - Sistema de Gestión de Ventas
 */

require_once 'includes/auth.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = trim($_POST['nick'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($nick) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        if (login($nick, $password)) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾 ComercioSys - Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>🧾 ComercioSys</h1>
                <p>Sistema de Gestión de Ventas</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="nick">
                        <i class="fas fa-user"></i>
                        Usuario
                    </label>
                    <input type="text" id="nick" name="nick" required 
                           value="<?php echo htmlspecialchars($_POST['nick'] ?? ''); ?>"
                           placeholder="Ingrese su usuario">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Ingrese su contraseña">
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>
            
            <div class="login-footer">
                <h3>Usuarios Disponibles:</h3>
                <div class="user-list">
                    <div class="user-item">
                        <strong>Administrador:</strong> Admin / 123456
                    </div>
                    <div class="user-item">
                        <strong>Soporte:</strong> user1 / 12345
                    </div>
                    <div class="user-item">
                        <strong>Caja:</strong> user2 / 1234
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>
