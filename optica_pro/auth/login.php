<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['usuario'])) {
    header("Location: /optica/optica_pro/index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND contrasena = ?");
    $stmt->execute([$usuario, $contrasena]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        
        // Redireccionar según rol
        switch($user['rol']) {
            case 'admin':
                header("Location: /optica/optica_pro/index.php");
                break;
            case 'optometrista':
                header("Location: /optica/optica_pro/modules/citas.php");
                break;
            case 'vendedor':
                header("Location: /optica/optica_pro/modules/ventas.php");
                break;
        }
        exit();
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OpticaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: var(--bg-main, #1A202C);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .login-container {
            background: var(--card-bg, #2D3748);
            padding: 40px;
            border-radius: 16px;
            box-shadow: var(--shadow, 0 4px 12px rgba(0,0,0,0.1));
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 4rem;
            color: var(--primary, #00BFA5);
            margin-bottom: 15px;
            display: block;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid var(--border-color, #4A5568);
            background-color: var(--bg-main, #1A202C);
            color: var(--text-dark, #E2E8F0);
        }
        .form-control:focus {
            border-color: var(--primary, #00BFA5);
            box-shadow: 0 0 0 3px rgba(0, 191, 165, 0.2);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            background-color: var(--primary, #00BFA5);
            border-color: var(--primary, #00BFA5);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-eye"></i>
            <h3 class="fw-bold" style="color: var(--text-dark);">OpticaPro</h3>
            <p style="color: var(--text-light);">Sistema de Gestión Óptica</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-user me-1" style="color: var(--text-light);"></i> Usuario
                </label>
                <input type="text" class="form-control" name="usuario" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-lock me-1" style="color: var(--text-light);"></i> Contraseña
                </label>
                <input type="password" class="form-control" name="contrasena" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-login">
                <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1" style="color: var(--text-light);"></i>
                Usuario: admin | Contraseña: 1234
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>