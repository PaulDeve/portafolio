<?php
require_once 'Model.php';

class Venta extends Model {
    public function crear($datos) {
        try {
            $this->conn->beginTransaction();

            // Insertar venta
            $sql = "INSERT INTO ventas (codigo, cliente_id, usuario_id, tipo_comprobante, subtotal, igv, total) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            // Si cliente_id está vacío (ej. "Cliente General"), se convierte a NULL para la BD.
            $cliente_id = empty($datos['cliente_id']) ? null : $datos['cliente_id'];

            $stmt = $this->query($sql, [
                $datos['codigo'],
                $cliente_id,
                $datos['usuario_id'],
                $datos['tipo_comprobante'],
                $datos['subtotal'],
                $datos['igv'],
                $datos['total']
            ]);
            
            $ventaId = $this->conn->lastInsertId();

            // Insertar detalles de venta
            foreach ($datos['productos'] as $producto) {
                $sql = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                
                $this->query($sql, [
                    $ventaId,
                    $producto['id'],
                    $producto['cantidad'],
                    $producto['precio_venta'],
                    $producto['subtotal']
                ]);

                // Actualizar stock
                $sql = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $this->query($sql, [$producto['cantidad'], $producto['id']]);
            }

            $this->conn->commit();
            return $ventaId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function obtenerVenta($id) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, u.nombre as vendedor 
                FROM ventas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                LEFT JOIN usuarios u ON v.usuario_id = u.id 
                WHERE v.id = ?";
        
        $venta = $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);

        if ($venta) {
            $sql = "SELECT dv.*, p.nombre, p.codigo 
                    FROM detalle_venta dv 
                    JOIN productos p ON dv.producto_id = p.id 
                    WHERE dv.venta_id = ?";
            
            $venta['detalles'] = $this->query($sql, [$id])->fetchAll(PDO::FETCH_ASSOC);
        }

        return $venta;
    }

    public function listarVentas() {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, u.nombre as vendedor 
                FROM ventas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                LEFT JOIN usuarios u ON v.usuario_id = u.id 
                ORDER BY v.fecha_venta DESC";
        
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasHoy() {
        $sql = "SELECT COALESCE(SUM(total), 0) as total 
                FROM ventas 
                WHERE DATE(fecha_venta) = CURDATE()";
        return $this->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerIngresosMes() {
        $sql = "SELECT COALESCE(SUM(total), 0) as total 
                FROM ventas 
                WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) 
                AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())";
        return $this->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerVentasSemana() {
        $sql = "SELECT DATE(fecha_venta) as fecha, COUNT(*) as cantidad, SUM(total) as total 
                FROM ventas 
                WHERE fecha_venta >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                GROUP BY DATE(fecha_venta) 
                ORDER BY fecha_venta";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosMasVendidos($limite = 5) {
        $sql = "SELECT p.nombre, SUM(dv.cantidad) as cantidad_vendida 
                FROM detalle_venta dv 
                JOIN productos p ON dv.producto_id = p.id 
                GROUP BY p.id 
                ORDER BY cantidad_vendida DESC 
                LIMIT ?";
        return $this->query($sql, [$limite])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUltimasVentas($limite = 5) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre 
                FROM ventas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                ORDER BY v.fecha_venta DESC 
                LIMIT ?";
        return $this->query($sql, [$limite])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generarCodigo() {
        $sql = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha_venta) = CURDATE()";
        $count = $this->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        $count++;
        return 'V' . date('Ymd') . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
?>