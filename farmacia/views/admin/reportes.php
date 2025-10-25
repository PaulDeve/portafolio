<?php
require_once '../../controllers/AuthController.php';
require_once '../../controllers/ReporteController.php';

$auth = new AuthController();
$auth->checkAuth();
$auth->checkRole(['administrador']);

$reporteController = new ReporteController();

// Definir fechas por defecto (mes actual)
$fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
$fechaFin = $_POST['fecha_fin'] ?? date('Y-m-t');

$reporteData = $reporteController->obtenerVentasPorFecha($fechaInicio, $fechaFin);
$ventas = $reporteData['ventas'];
$totales = $reporteData['totales'];
$chartData = $reporteData['chartData'];

include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div id="page-content-wrapper">
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fs-4 mb-0">Reporte de Ventas</h3>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control app-form-control" value="<?php echo htmlspecialchars($fechaInicio); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control app-form-control" value="<?php echo htmlspecialchars($fechaFin); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Generar Reporte</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards de Resumen -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-cash-stack me-2"></i>Total Ventas</h5>
                        <p class="card-text fs-4">S/ <?php echo number_format($totales['total_ventas'], 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-receipt me-2"></i>Número de Ventas</h5>
                        <p class="card-text fs-4"><?php echo $totales['numero_ventas']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-cart-check me-2"></i>Ticket Promedio</h5>
                        <p class="card-text fs-4">S/ <?php echo number_format($totales['ticket_promedio'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-bar-chart-line-fill me-1"></i>
                Ventas por Día
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="100%" height="30"></canvas>
            </div>
        </div>

        <!-- Tabla de Ventas -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-table me-1"></i>
                Detalle de Ventas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="salesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID Venta</th>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ventas)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay ventas en el rango de fechas seleccionado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($venta['id']); ?></td>
                                        <td><?php echo htmlspecialchars($venta['codigo']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($venta['fecha_venta']))); ?></td>
                                        <td><?php echo htmlspecialchars($venta['cliente_nombre'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($venta['vendedor_nombre']); ?></td>
                                        <td>S/ <?php echo number_format($venta['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>

<!-- Incluir Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Configuración del gráfico
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chartData['labels']); ?>,
            datasets: [{
                label: 'Total Ventas (S/)',
                data: <?php echo json_encode($chartData['data']); ?>,
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'S/ ' + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Inicializar DataTables
    // new DataTable('#salesTable'); // Descomentar si DataTables está configurado
});
</script>