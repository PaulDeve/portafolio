<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$db = $conexion_global->getPdo();
$id = $_GET['id'] ?? null;
$cliente = null;
if ($id) {
  $stmt = $db->prepare('SELECT * FROM clientes WHERE id_cliente = ?');
  $stmt->execute([$id]);
  $cliente = $stmt->fetch();
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2><?= $cliente ? 'Editar' : 'Nuevo' ?> Cliente</h2>
  <a href="/Ferreteria/views/clientes.php" class="btn btn-secondary">Volver</a>
</div>

<form action="/Ferreteria/controllers/clientesController.php" method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <input type="hidden" name="id_cliente" value="<?= $cliente['id_cliente'] ?? '' ?>">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input class="form-control" name="nombre" required value="<?= htmlspecialchars($cliente['nombre'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">DNI</label>
    <input class="form-control" name="dni" value="<?= htmlspecialchars($cliente['dni'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input class="form-control" name="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Dirección</label>
    <input class="form-control" name="direccion" value="<?= htmlspecialchars($cliente['direccion'] ?? '') ?>">
  </div>
  <div class="d-grid">
    <button class="btn btn-success">Guardar</button>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
