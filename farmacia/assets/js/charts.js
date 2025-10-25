document.addEventListener('DOMContentLoaded', function() {
    // Solo ejecutar si tenemos datos de gráficos en la página
    if (typeof chartData === 'undefined') {
        return;
    }

    // --- CONFIGURACIÓN GLOBAL DE CHART.JS PARA TEMA OSCURO ---
    Chart.defaults.color = '#a0a0a0';
    Chart.defaults.borderColor = '#333';

    // --- 1. GRÁFICO DE VENTAS DIARIAS (LÍNEAS) ---
    const dailySalesCtx = document.getElementById('dailySalesChart');
    if (dailySalesCtx) {
        const dailySalesLabels = chartData.ventas_diarias.map(item => new Date(item.fecha).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' }));
        const dailySalesData = chartData.ventas_diarias.map(item => item.total_dia);

        new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: dailySalesLabels,
                datasets: [{
                    label: 'Ingresos',
                    data: dailySalesData,
                    fill: true,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.3,
                    pointBackgroundColor: '#0d6efd',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '$' + value; }
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1e1e',
                        titleColor: '#e0e0e0',
                        bodyColor: '#e0e0e0',
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // --- 2. GRÁFICO DE PRODUCTOS MÁS VENDIDOS (DONA) ---
    const topProductsCtx = document.getElementById('topProductsChart');
    if (topProductsCtx) {
        const topProductsLabels = chartData.productos_mas_vendidos.map(item => item.nombre);
        const topProductsData = chartData.productos_mas_vendidos.map(item => item.total_vendido);

        new Chart(topProductsCtx, {
            type: 'doughnut',
            data: {
                labels: topProductsLabels,
                datasets: [{
                    data: topProductsData,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0'],
                    hoverOffset: 4
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
    }

    // --- 3. GRÁFICO DE INGRESOS POR CATEGORÍA (BARRAS) ---
    const categorySalesCtx = document.getElementById('categorySalesChart');
    if (categorySalesCtx) {
        const categoryLabels = chartData.ventas_por_categoria.map(item => item.categoria);
        const categoryData = chartData.ventas_por_categoria.map(item => item.total_categoria);

        new Chart(categorySalesCtx, {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Ingresos por Categoría',
                    data: categoryData,
                    backgroundColor: 'rgba(13, 110, 253, 0.6)',
                    borderColor: '#0d6efd',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '$' + value; }
                        }
                    }
                }
            }
        });
    }
});