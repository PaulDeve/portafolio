<?php
require_once 'includes/header.php';
require_once 'config/db.php';

// Obtener estadísticas
$total_clientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$total_ventas = $pdo->query("SELECT COUNT(*) FROM ventas")->fetchColumn();
$total_citas = $pdo->query("SELECT COUNT(*) FROM citas WHERE estado = 'pendiente'")->fetchColumn();

// Datos para gráficos
$ventas_mes = $pdo->query("
    SELECT MONTH(fecha) as mes, COUNT(*) as total 
    FROM ventas 
    WHERE YEAR(fecha) = YEAR(CURRENT_DATE)
    GROUP BY MONTH(fecha)
")->fetchAll(PDO::FETCH_ASSOC);

// Normalizar a 12 meses (Ene..Dic) para que los datos coincidan con las labels
$ventas_totales = array_fill(0, 12, 0);
foreach ($ventas_mes as $vm) {
    $m = (int)$vm['mes'];
    $ventas_totales[$m - 1] = (int)$vm['total'];
}

$citas_estado = $pdo->query("
    SELECT estado, COUNT(*) as total 
    FROM citas 
    GROUP BY estado
")->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content flex-grow-1">
            <?php include 'includes/navbar.php'; ?>
            
            <div class="container-fluid">
                <h2 class="mb-4">
                    <i class="fas fa-tachometer-alt" style="color: var(--primary);"></i> Dashboard
                </h2>
                
                <!-- Tarjetas de estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Total Clientes</h6>
                                        <h3 class="fw-bold mb-0"><?php echo $total_clientes; ?></h3>
                                    </div>
                                    <i class="fas fa-users fa-2x" style="color: var(--primary);"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Total Productos</h6>
                                        <h3 class="fw-bold mb-0"><?php echo $total_productos; ?></h3>
                                    </div>
                                    <i class="fas fa-glasses fa-2x" style="color: var(--primary);"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Total Ventas</h6>
                                        <h3 class="fw-bold mb-0"><?php echo $total_ventas; ?></h3>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-2x" style="color: var(--primary);"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6>Citas Pendientes</h6>
                                        <h3 class="fw-bold mb-0"><?php echo $total_citas; ?></h3>
                                    </div>
                                    <i class="fas fa-calendar-check fa-2x" style="color: var(--primary);"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráficos -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">Ventas por Mes</div>
                            <div class="card-body">
                                <canvas id="ventasChart" class="chart-canvas"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">Estado de Citas</div>
                            <div class="card-body">
                                <canvas id="citasChart" class="chart-canvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de ventas por mes
        const ventasCtx = document.getElementById('ventasChart').getContext('2d');
        const ventasChart = new Chart(ventasCtx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Ventas',
                    data: [<?php echo implode(',', $ventas_totales); ?>],
                    borderColor: null, // se asigna dinámicamente desde CSS
                    backgroundColor: null,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: null
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-light') || '#617078' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-light') || '#617078' },
                        grid: { color: 'rgba(255,255,255,0.03)' }
                    }
                }
            }
        });
        // Aplicar colores desde variables CSS para respetar tema claro/oscuro
        (function(){
            const root = getComputedStyle(document.documentElement);
            const primary = (root.getPropertyValue('--primary') || '#188FA3').trim();
            function hexToRgba(hex, alpha){
                let h = hex.replace('#','');
                if(h.length === 3) h = h.split('').map(c=>c+c).join('');
                const bigint = parseInt(h,16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }
            const bg = hexToRgba(primary, 0.12);
            ventasChart.data.datasets[0].borderColor = primary;
            ventasChart.data.datasets[0].backgroundColor = bg;
            ventasChart.data.datasets[0].pointBackgroundColor = primary;
            ventasChart.update();
        })();
        
        // Gráfico de estado de citas
        const citasCtx = document.getElementById('citasChart').getContext('2d');
        const citasChart = new Chart(citasCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php echo "'" . implode("','", array_column($citas_estado, 'estado')) . "'"; ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_column($citas_estado, 'total')); ?>],
                    backgroundColor: ['#A0AEC0', '#00BFA5', '#E53E3E']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

    <?php include 'includes/footer.php'; ?>