<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$db = $conexion_global->getPdo();
$id = $_GET['id'] ?? null;
$prov = null;
if ($id) {
  $stmt = $db->prepare('SELECT * FROM proveedores WHERE id_proveedor = ?');
  $stmt->execute([$id]);
  $prov = $stmt->fetch();
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2><?= $prov ? 'Editar' : 'Nuevo' ?> Proveedor</h2>
  <a href="/Ferreteria/views/proveedores.php" class="btn btn-secondary">Volver</a>
</div>

<form action="/Ferreteria/controllers/proveedoresController.php" method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <input type="hidden" name="id_proveedor" value="<?= $prov['id_proveedor'] ?? '' ?>">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input class="form-control" name="nombre" required value="<?= htmlspecialchars($prov['nombre'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input class="form-control" name="telefono" value="<?= htmlspecialchars($prov['telefono'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Dirección</label>
    <input class="form-control" name="direccion" value="<?= htmlspecialchars($prov['direccion'] ?? '') ?>">
  </div>
  <div class="d-grid">
    <button class="btn btn-success">Guardar</button>
  </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
