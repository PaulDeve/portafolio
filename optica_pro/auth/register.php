<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Solo administradores pueden registrar usuarios
if ($_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    if ($stmt->fetchColumn() > 0) {
        $mensaje = '<div class="alert alert-danger">El nombre de usuario ya está en uso.</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, usuario, contrasena, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $usuario, $contrasena, $rol]);
        $mensaje = '<div class="alert alert-success">Usuario registrado con éxito.</div>';
    }
}
?>

<body>
    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content flex-grow-1">
            <?php include '../includes/navbar.php'; ?>
            <div class="container-fluid">
                <h2><i class="fas fa-user-plus" style="color: var(--primary);"></i> Registrar Nuevo Usuario</h2>

                <?php if ($mensaje): ?>
                    <?php echo $mensaje; ?>
                <?php endif; ?>

                <div class="card mt-4" style="max-width: 600px;">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="usuario" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="contrasena" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select name="rol" class="form-select" required>
                                    <option value="vendedor">Vendedor</option>
                                    <option value="optometrista">Optometrista</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>