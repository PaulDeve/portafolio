<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Total de ventas por mes
$ventas_mes = $pdo->query("
    SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as total_ventas, SUM(total) as ingresos
    FROM ventas
    GROUP BY mes
    ORDER BY mes DESC
    LIMIT 12
")->fetchAll(PDO::FETCH_ASSOC);

// Top productos más vendidos
$top_productos = $pdo->query("
    SELECT p.nombre, SUM(dv.cantidad) as total_vendido
    FROM detalle_venta dv
    JOIN productos p ON dv.id_producto = p.id_producto
    GROUP BY p.id_producto
    ORDER BY total_vendido DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Total clientes
$total_clientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$total_ventas = $pdo->query("SELECT COUNT(*) FROM ventas")->fetchColumn();
$total_ingresos = $pdo->query("SELECT SUM(total) FROM ventas")->fetchColumn();
?>

    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content flex-grow-1">
            <?php include '../includes/navbar.php'; ?>
            <div class="container-fluid">
                <h2><i class="fas fa-chart-bar" style="color: var(--primary);"></i> Reportes y Estadísticas</h2>

                <div class="row mb-4 report-stats">
                    <div class="col-md-3 mb-3">
                        <div class="card p-2">
                            <div class="card-body p-1 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small">Total Clientes</div>
                                    <div class="fw-bold fs-5"><?= $total_clientes ?></div>
                                </div>
                                <i class="fas fa-users" style="color: var(--primary); font-size:1.2rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card p-2">
                            <div class="card-body p-1 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small">Total Productos</div>
                                    <div class="fw-bold fs-5"><?= $total_productos ?></div>
                                </div>
                                <i class="fas fa-glasses" style="color: var(--primary); font-size:1.2rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card p-2">
                            <div class="card-body p-1 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small">Total Ventas</div>
                                    <div class="fw-bold fs-5"><?= $total_ventas ?></div>
                                </div>
                                <i class="fas fa-shopping-cart" style="color: var(--primary); font-size:1.2rem;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card p-2">
                            <div class="card-body p-1 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small">Total Ingresos</div>
                                    <div class="fw-bold fs-5">S/ <?= number_format($total_ingresos, 2) ?></div>
                                </div>
                                <i class="fas fa-coins" style="color: var(--primary); font-size:1.2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row report-charts">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Ingresos por Mes</span>
                                <div>
                                    <a href="reportes_print.php" target="_blank" class="btn btn-outline-secondary btn-sm me-2">Generar PDF</a>
                                    <a href="reportes_export.php?type=excel" class="btn btn-outline-primary btn-sm">Exportar Excel</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="ventasMesChart" class="chart-canvas"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Productos Más Vendidos</div>
                            <div class="card-body">
                                <canvas id="productosChart" class="chart-canvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        const ventasMesCtx = document.getElementById('ventasMesChart').getContext('2d');
        new Chart(ventasMesCtx, {
            type: 'bar',
            data: {
                labels: [<?= '"' . implode('","', array_column($ventas_mes, 'mes')) . '"' ?>],
                datasets: [{
                    label: 'Ingresos (S/)', 
                    data: [<?= implode(',', array_column($ventas_mes, 'ingresos')) ?>],
                    backgroundColor: null
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const productosCtx = document.getElementById('productosChart').getContext('2d');
        new Chart(productosCtx, {
            type: 'doughnut',
            data: {
                labels: [<?= '"' . implode('","', array_column($top_productos, 'nombre')) . '"' ?>],
                datasets: [{
                    data: [<?= implode(',', array_column($top_productos, 'total_vendido')) ?>],
                    backgroundColor: ['#00BFA5', '#007B8A', '#A0AEC0', '#4A5568', '#E2E8F0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>

    