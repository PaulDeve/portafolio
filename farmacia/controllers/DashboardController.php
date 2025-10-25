<?php
require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController {
    private $dashboardModel;

    public function __construct() {
        $this->dashboardModel = new Dashboard();
    }

    public function getDashboardData() {
        $stats = [
            'total_productos' => $this->dashboardModel->getTotalProductos(),
            'ventas_hoy' => $this->dashboardModel->getVentasHoy(),
            'ingresos_mes' => $this->dashboardModel->getIngresosMes(),
            'productos_por_vencer' => $this->dashboardModel->getProductosPorVencer(),
        ];

        $charts = [
            'ventas_diarias' => $this->dashboardModel->getVentasDiarias(7),
            'productos_mas_vendidos' => $this->dashboardModel->getProductosMasVendidos(5),
            'ventas_por_categoria' => $this->dashboardModel->getVentasPorCategoria(),
        ];

        return ['stats' => $stats, 'charts' => $charts];
    }
}
?>