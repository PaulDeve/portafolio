<?php
require_once __DIR__ . '/Model.php';

class Dashboard extends Model {

    public function getTotalProductos() {
        $stmt = $this->query("SELECT COUNT(id) as total FROM productos WHERE estado = 1");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getVentasHoy() {
        $stmt = $this->query("SELECT COUNT(id) as total FROM ventas WHERE DATE(fecha_venta) = CURDATE() AND estado = 1");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getIngresosMes() {
        $stmt = $this->query("SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha_venta) = MONTH(CURDATE()) AND YEAR(fecha_venta) = YEAR(CURDATE()) AND estado = 1");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;
    }

    public function getProductosPorVencer() {
        $stmt = $this->query("SELECT COUNT(id) as total FROM productos WHERE estado = 1 AND fecha_vencimiento BETWEEN CURDATE() AND CURDATE() + INTERVAL 30 DAY");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getVentasDiarias(int $dias = 7) {
        $sql = "SELECT DATE(fecha_venta) as fecha, SUM(total) as total_dia
                FROM ventas
                WHERE fecha_venta >= CURDATE() - INTERVAL :dias DAY AND estado = 1
                GROUP BY DATE(fecha_venta)
                ORDER BY fecha ASC";
        $stmt = $this->query($sql, [':dias' => $dias]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductosMasVendidos(int $limit = 5) {
        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido
                FROM detalle_venta dv
                JOIN productos p ON dv.producto_id = p.id
                GROUP BY p.nombre
                ORDER BY total_vendido DESC
                LIMIT :limit";
        $stmt = $this->query($sql, [':limit' => $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVentasPorCategoria() {
        $sql = "SELECT p.categoria, SUM(dv.subtotal_linea) as total_categoria
                FROM detalle_venta dv
                JOIN productos p ON dv.producto_id = p.id
                GROUP BY p.categoria
                HAVING total_categoria > 0
                ORDER BY total_categoria DESC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>