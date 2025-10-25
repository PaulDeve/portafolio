<?php
require_once __DIR__ . '/../config/conexion.php';
// login.php
if (session_status() == PHP_SESSION_NONE) session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario = trim($_POST['usuario'] ?? '');
  $contrasena = $_POST['contrasena'] ?? '';
  if ($usuario && $contrasena) {
    $db = $conexion_global->getPdo();
    // Buscar usuario sin distinguir mayúsculas/minúsculas
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE LOWER(usuario) = LOWER(?) LIMIT 1');
    $stmt->execute([$usuario]);
    $u = $stmt->fetch();
    if (!$u) {
      $error = 'Usuario no encontrado';
    } elseif (!password_verify($contrasena, $u['contrasena'])) {
      $error = 'Contraseña incorrecta';
    } else {
      // Iniciar sesión
      $_SESSION['usuario'] = $u['usuario'];
      $_SESSION['id_usuario'] = $u['id_usuario'];
      $_SESSION['rol'] = $u['rol'];
      header('Location: dashboard.php');
      exit;
    }
  } else {
    $error = 'Completa usuario y contraseña';
  }
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card mt-5">
      <div class="card-body">
        <h4 class="card-title mb-3">Iniciar sesión</h4>
        <?php if($error): ?>
          <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
        <?php endif; ?>
        <form method="post" novalidate>
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="contrasena" class="form-control" required>
          </div>
          <div class="d-grid">
            <button class="btn btn-primary">Ingresar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
