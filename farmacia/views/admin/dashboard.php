<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
require_once __DIR__ . '/../../controllers/DashboardController.php';

// Solo los administradores pueden ver esta página
if (strtolower($userRole) !== 'administrador') {
    echo "<script>window.location.href = '../auth/login.php';</script>";
    exit;
}

$dashboardController = new DashboardController();
$data = $dashboardController->getDashboardData();

$stats = $data['stats'];
$chartsData = $data['charts'];

?>

<div class="container-fluid">
    <!-- Título de la página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bi bi-download me-2"></i>Generar Reporte</a>
    </div>

    <!-- Fila de Tarjetas de Métricas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Productos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_productos'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-capsule-pill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventas del Día</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['ventas_hoy'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ingresos del Mes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?= number_format($stats['ingresos_mes'], 2) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Productos por Vencer (30d)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['productos_por_vencer'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila de Gráficos -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas de la Última Semana</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Productos Más Vendidos</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <div class="row">
        <div class="col-lg-12">
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ingresos por Categoría</h6>
                </div>
                <div class="card-body">
                    <canvas id="categorySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Pasar datos de PHP a JavaScript -->
<script>
    const chartData = <?= json_encode($chartsData) ?>;
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
