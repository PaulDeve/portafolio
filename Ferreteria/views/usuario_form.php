<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$db = $conexion_global->getPdo();
$id = $_GET['id'] ?? null;
$user = null;
if ($id) {
  $stmt = $db->prepare('SELECT id_usuario,nombre_usuario,usuario,rol FROM usuarios WHERE id_usuario = ?');
  $stmt->execute([$id]);
  $user = $stmt->fetch();
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2><?= $user ? 'Editar' : 'Nuevo' ?> Usuario</h2>
  <a href="/Ferreteria/views/usuarios.php" class="btn btn-secondary">Volver</a>
</div>

<form action="/Ferreteria/controllers/usuariosController.php" method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <input type="hidden" name="id_usuario" value="<?= $user['id_usuario'] ?? '' ?>">
  <div class="mb-3">
    <label class="form-label">Nombre completo</label>
    <input class="form-control" name="nombre_usuario" required value="<?= htmlspecialchars($user['nombre_usuario'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Usuario (login)</label>
    <input class="form-control" name="usuario" required value="<?= htmlspecialchars($user['usuario'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Contraseña <?= $user ? '(dejar en blanco para no cambiar)':'' ?></label>
    <input class="form-control" name="contrasena" type="password">
  </div>
  <div class="mb-3">
    <label class="form-label">Rol</label>
    <select name="rol" class="form-select">
      <option value="empleado" <?= (isset($user['rol']) && $user['rol']=='empleado')? 'selected':'' ?>>Empleado</option>
      <option value="admin" <?= (isset($user['rol']) && $user['rol']=='admin')? 'selected':'' ?>>Administrador</option>
    </select>
  </div>
  <div class="d-grid">
    <button class="btn btn-success">Guardar</button>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
