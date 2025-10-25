<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Reportes</h2>

<form method="get" action="reportes.php" class="row g-3 mb-3">
  <div class="col-md-3">
    <label>Desde</label>
    <input type="date" name="desde" class="form-control" value="<?= $_GET['desde'] ?? '' ?>">
  </div>
  <div class="col-md-3">
    <label>Hasta</label>
    <input type="date" name="hasta" class="form-control" value="<?= $_GET['hasta'] ?? '' ?>">
  </div>
  <div class="col-md-3 align-self-end">
    <button class="btn btn-primary">Generar</button>
  </div>
</form>

<?php
$db = $conexion_global->getPdo();
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;
if ($desde || $hasta) {
  $where = [];
  $params = [];
  if ($desde) { $where[] = 'fecha >= ?'; $params[] = $desde.' 00:00:00'; }
  if ($hasta) { $where[] = 'fecha <= ?'; $params[] = $hasta.' 23:59:59'; }
  $whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';
  // Ventas por fecha (listado)
  $stmt = $db->prepare("SELECT * FROM ventas $whereSql ORDER BY fecha DESC");
  $stmt->execute($params);
  $ventas = $stmt->fetchAll();
  // Total ingresos
  $stmt = $db->prepare("SELECT IFNULL(SUM(total),0) as total FROM ventas $whereSql");
  $stmt->execute($params);
  $totalIngresos = $stmt->fetchColumn();
  // Productos más vendidos
  $sql = "SELECT dv.id_producto, p.nombre, SUM(dv.cantidad) as total_vendido, SUM(dv.subtotal) as ingresos FROM detalle_venta dv JOIN productos p ON dv.id_producto = p.id_producto JOIN ventas v ON dv.id_venta = v.id_venta $whereSql GROUP BY dv.id_producto ORDER BY total_vendido DESC LIMIT 20";
  $stmt = $db->prepare($sql);
  $stmt->execute($params);
  $top = $stmt->fetchAll();
  ?>

  <div class="mb-4">
    <div class="print-header" style="display:none;">
      <div class="title">Reporte de ventas</div>
      <div class="meta">Rango: <?= htmlspecialchars($desde ?: '---') ?> — <?= htmlspecialchars($hasta ?: '---') ?> | Generado: <?= date('Y-m-d H:i') ?></div>
    </div>
    <h4>Resumen</h4>
    <p>Total ingresos: $<?= number_format($totalIngresos,2) ?></p>
    <a class="btn btn-outline-secondary" href="/Ferreteria/controllers/reportesController.php?format=csv&desde=<?=urlencode($desde)?>&hasta=<?=urlencode($hasta)?>">Exportar CSV</a>
    <button class="btn btn-outline-primary" onclick="window.print()">Imprimir</button>
  </div>

  <h5>Ventas</h5>
  <table class="table table-sm">
    <thead><tr><th>ID</th><th>Fecha</th><th>Total</th></tr></thead>
    <tbody>
      <?php foreach($ventas as $v): ?>
      <tr><td><?= $v['id_venta'] ?></td><td><?= $v['fecha'] ?></td><td>$<?= number_format($v['total'],2) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h5>Productos más vendidos</h5>
  <table class="table table-sm">
    <thead><tr><th>Producto</th><th>Cantidad vendida</th><th>Ingresos</th></tr></thead>
    <tbody>
      <?php foreach($top as $t): ?>
      <tr><td><?= htmlspecialchars($t['nombre']) ?></td><td><?= $t['total_vendido'] ?></td><td>$<?= number_format($t['ingresos'],2) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php } else { ?>
  <p>Selecciona un rango de fechas para generar el reporte.</p>
<?php } ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
