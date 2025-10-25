<?php
require_once __DIR__ . '/Model.php';

class Producto extends Model {

    /**
     * Obtiene todos los productos activos.
     * @return array
     */
    public function getAll() {
        $sql = "SELECT * FROM productos ORDER BY id DESC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un producto por su ID.
     * @param int $id
     * @return mixed
     */
    public function getById(int $id) {
        $sql = "SELECT * FROM productos WHERE id = :id";
        return $this->query($sql, [':id' => $id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo producto.
     * @param array $data
     * @return string El ID del nuevo producto.
     */
    public function create(array $data) {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, categoria, stock, stock_minimo, precio_compra, precio_venta, fecha_vencimiento, proveedor)
                VALUES (:codigo, :nombre, :descripcion, :categoria, :stock, :stock_minimo, :precio_compra, :precio_venta, :fecha_vencimiento, :proveedor)";
        
        $this->query($sql, [
            ':codigo' => $data['codigo'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':categoria' => $data['categoria'],
            ':stock' => $data['stock'],
            ':stock_minimo' => $data['stock_minimo'],
            ':precio_compra' => $data['precio_compra'],
            ':precio_venta' => $data['precio_venta'],
            ':fecha_vencimiento' => $data['fecha_vencimiento'],
            ':proveedor' => $data['proveedor']
        ]);

        return $this->conn->lastInsertId();
    }

    /**
     * Actualiza un producto existente.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data) {
        $sql = "UPDATE productos SET 
                    codigo = :codigo, 
                    nombre = :nombre, 
                    descripcion = :descripcion, 
                    categoria = :categoria, 
                    stock = :stock, 
                    stock_minimo = :stock_minimo,
                    precio_compra = :precio_compra, 
                    precio_venta = :precio_venta, 
                    fecha_vencimiento = :fecha_vencimiento, 
                    proveedor = :proveedor,
                    estado = :estado
                WHERE id = :id";

        $stmt = $this->query($sql, [
            ':codigo' => $data['codigo'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':categoria' => $data['categoria'],
            ':stock' => $data['stock'],
            ':stock_minimo' => $data['stock_minimo'],
            ':precio_compra' => $data['precio_compra'],
            ':precio_venta' => $data['precio_venta'],
            ':fecha_vencimiento' => $data['fecha_vencimiento'],
            ':proveedor' => $data['proveedor'],
            ':estado' => $data['estado'],
            ':id' => $id
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina un producto de la base de datos (borrado físico).
     * @param int $id
     * @return bool
     */
    public function delete(int $id) {
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->query($sql, [':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // Otros métodos de búsqueda y utilidad pueden permanecer aquí si son necesarios para otras partes del sistema
}
?>