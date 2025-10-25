<?php
require_once 'Model.php';

class Cliente extends Model {
    public function listar() {
        $sql = "SELECT * FROM clientes ORDER BY nombre ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener($id) {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO clientes (dni, nombre, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $datos['dni'],
            $datos['nombre'],
            $datos['telefono'],
            $datos['email'],
            $datos['direccion']
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE clientes SET 
                dni = ?, 
                nombre = ?, 
                telefono = ?, 
                email = ?, 
                direccion = ?,
                estado = ? 
                WHERE id = ?";

        return $this->query($sql, [
            $datos['dni'],
            $datos['nombre'],
            $datos['telefono'],
            $datos['email'],
            $datos['direccion'],
            $datos['estado'],
            $id
        ]);
    }

    public function eliminar($id) {
        $sql = "UPDATE clientes SET estado = 0 WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    public function buscarPorDni($dni) {
        $sql = "SELECT * FROM clientes WHERE dni = ? AND estado = 1";
        return $this->query($sql, [$dni])->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorNombre($nombre) {
        $sql = "SELECT * FROM clientes WHERE nombre LIKE ? AND estado = 1";
        return $this->query($sql, ["%$nombre%"])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialCompras($id) {
        $sql = "SELECT v.*, COUNT(dv.id) as items 
                FROM ventas v 
                LEFT JOIN detalle_venta dv ON v.id = dv.venta_id 
                WHERE v.cliente_id = ? 
                GROUP BY v.id 
                ORDER BY v.fecha_venta DESC";
        return $this->query($sql, [$id])->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>