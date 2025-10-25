<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) {
  header('Location: login.php'); exit;
}
$db = $conexion_global->getPdo();
// Obtener métricas
$totales = [];
$totales['productos'] = $db->query('SELECT COUNT(*) FROM productos')->fetchColumn();
$totales['clientes'] = $db->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
$totales['ventas'] = $db->query('SELECT COUNT(*) FROM ventas')->fetchColumn();
$totales['ingresos'] = $db->query('SELECT IFNULL(SUM(total),0) FROM ventas')->fetchColumn();

// Datos para gráficos
$ventas_dias = $db->query("SELECT DATE(fecha) as dia, SUM(total) as total FROM ventas GROUP BY DATE(fecha) ORDER BY dia DESC LIMIT 7")->fetchAll();
$top = $db->query("SELECT p.nombre, SUM(dv.cantidad) as vendido FROM detalle_venta dv JOIN productos p ON dv.id_producto = p.id_producto GROUP BY dv.id_producto ORDER BY vendido DESC LIMIT 5")->fetchAll();
// Extra widgets: promedio ticket, ventas hoy, stock bajo
$avgTicket = $db->query("SELECT IFNULL(AVG(total),0) FROM ventas")->fetchColumn();
$today = date('Y-m-d');
$stmt = $db->prepare("SELECT COUNT(*) as cnt, IFNULL(SUM(total),0) as sum FROM ventas WHERE DATE(fecha)=?");
$stmt->execute([$today]);
$ventas_hoy = $stmt->fetch();
$low_stock = $db->query("SELECT id_producto,nombre,stock FROM productos WHERE stock <= 5 ORDER BY stock ASC LIMIT 5")->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
  <div class="col-12">
    <h1>Dashboard</h1>
    <p>Bienvenido, <?=htmlspecialchars($_SESSION['usuario'])?></p>
  </div>
  <div class="col-md-3">
    <div class="card card-summary text-bg-primary mb-3">
      <div class="card-body">
        <h5 class="card-title">Productos</h5>
        <p class="card-text display-6"><?=intval($totales['productos'])?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary text-bg-success mb-3">
      <div class="card-body">
        <h5 class="card-title">Clientes</h5>
        <p class="card-text display-6"><?=intval($totales['clientes'])?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary text-bg-warning mb-3">
      <div class="card-body">
        <h5 class="card-title">Ventas</h5>
        <p class="card-text display-6"><?=intval($totales['ventas'])?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-summary text-bg-info mb-3">
      <div class="card-body">
        <h5 class="card-title">Ingresos</h5>
        <p class="card-text display-6">$<?=number_format($totales['ingresos'],2)?></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-body">
        <h5>Estadísticas</h5>
        <div class="dashboard-charts mt-3">
          <div class="row">
            <div class="col-md-7">
              <?php if (empty($ventas_dias)): ?>
                <div class="p-4 text-muted">No hay datos de ventas para mostrar. Registra ventas para ver las estadísticas.</div>
              <?php else: ?>
                <div class="chart-area mb-3" style="height:300px;">
                  <canvas id="ventasChart" style="max-width:100%;height:100%;"></canvas>
                </div>
              <?php endif; ?>
            </div>

            <div class="col-md-5">
              <?php if (empty($top)): ?>
                <div class="p-4 text-muted">No hay productos vendidos para mostrar.</div>
              <?php else: ?>
                <div class="chart-area mb-3 d-flex align-items-center justify-content-center" style="height:320px;">
                  <canvas id="productosChart" style="max-width:320px;max-height:320px;width:100%;height:100%;"></canvas>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5>Últimas ventas</h5>
        <?php $ult = $db->query('SELECT v.id_venta,v.fecha,v.total,c.nombre as cliente,u.usuario as vendedor FROM ventas v LEFT JOIN clientes c ON v.id_cliente=c.id_cliente LEFT JOIN usuarios u ON v.id_usuario=u.id_usuario ORDER BY v.fecha DESC LIMIT 5')->fetchAll(); ?>
        <table class="table table-sm">
          <thead><tr><th>ID</th><th>Fecha</th><th>Total</th><th>Vendedor</th></tr></thead>
          <tbody>
            <?php foreach($ult as $r): ?>
            <tr><td><?= $r['id_venta'] ?></td><td><?= $r['fecha'] ?></td><td>$<?= number_format($r['total'],2) ?></td><td><?= htmlspecialchars($r['vendedor'] ?? '—') ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
const ventasLabels = <?= json_encode(array_reverse(array_column($ventas_dias,'dia'))) ?>;
const ventasData = <?= json_encode(array_reverse(array_column($ventas_dias,'total'))) ?>;
const topLabels = <?= json_encode(array_column($top,'nombre')) ?>;
const topData = <?= json_encode(array_column($top,'vendido')) ?>;

// Inicializar gráficos sólo si hay datos
window.addEventListener('load', function(){
  if (typeof Chart === 'undefined') return; // safety
  if (ventasData.length > 0 && document.getElementById('ventasChart')) {
    new Chart(document.getElementById('ventasChart'), {
      type: 'bar',
      data: { labels: ventasLabels, datasets: [{ label: 'Ingresos', data: ventasData, backgroundColor: 'rgba(54, 162, 235, 0.7)' }] },
      options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
    });
  }

  if (topData.length > 0 && document.getElementById('productosChart')) {
    new Chart(document.getElementById('productosChart'), {
      type: 'pie',
      data: { labels: topLabels, datasets: [{ data: topData, backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6f42c1'] }] },
      options: { responsive:true, maintainAspectRatio:false }
    });
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
// Debug helper: mostrar datos crudos si se pasa ?debug=1 (solo para diagnóstico)
if (isset($_GET['debug']) && $_GET['debug']=='1') {
  echo '<div class="app-container" style="max-width:1100px;margin:20px auto;">';
  echo '<h4>DEBUG: datos crudos para estadísticas</h4>';
  echo '<h5>$ventas_dias</h5><pre>' . htmlspecialchars(var_export($ventas_dias, true)) . '</pre>';
  echo '<h5>$top</h5><pre>' . htmlspecialchars(var_export($top, true)) . '</pre>';
  echo '</div>';
}
?>
