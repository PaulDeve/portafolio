<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Producto.php';

class ProductoController {
    private $productoModel;

    public function __construct() {
        $this->productoModel = new Producto();
        // Proteger todas las acciones con verificación de rol
        if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'administrador') {
            $this->jsonResponse(403, ['error' => 'Acceso denegado']);
            exit;
        }
    }

    private function jsonResponse($code, $data) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }

    public function index() {
        $productos = $this->productoModel->getAll();
        $this->jsonResponse(200, ['data' => $productos]);
    }

    public function store() {
        $data = $_POST;
        // TODO: Agregar validación de datos
        $newProductId = $this->productoModel->create($data);
        if ($newProductId) {
            $newProduct = $this->productoModel->getById($newProductId);
            $this->jsonResponse(201, ['status' => 'success', 'message' => 'Producto creado exitosamente.', 'product' => $newProduct]);
        } else {
            $this->jsonResponse(500, ['status' => 'error', 'message' => 'No se pudo crear el producto.']);
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        if ($id > 0) {
            $product = $this->productoModel->getById($id);
            if ($product) {
                $this->jsonResponse(200, $product);
            } else {
                $this->jsonResponse(404, ['error' => 'Producto no encontrado.']);
            }
        } else {
            $this->jsonResponse(400, ['error' => 'ID de producto no válido.']);
        }
    }

    public function update() {
        $id = $_POST['producto_id'] ?? 0;
        if ($id > 0) {
            $data = $_POST;
            // TODO: Agregar validación de datos
            if ($this->productoModel->update($id, $data)) {
                $updatedProduct = $this->productoModel->getById($id);
                $this->jsonResponse(200, ['status' => 'success', 'message' => 'Producto actualizado exitosamente.', 'product' => $updatedProduct]);
            } else {
                $this->jsonResponse(500, ['status' => 'error', 'message' => 'No se pudo actualizar el producto.']);
            }
        } else {
            $this->jsonResponse(400, ['error' => 'ID de producto no válido.']);
        }
    }

    public function delete() {
        $id = $_POST['id'] ?? 0;
        if ($id > 0) {
            if ($this->productoModel->delete($id)) {
                $this->jsonResponse(200, ['status' => 'success', 'message' => 'Producto eliminado exitosamente.']);
            } else {
                $this->jsonResponse(500, ['status' => 'error', 'message' => 'No se pudo eliminar el producto.']);
            }
        } else {
            $this->jsonResponse(400, ['error' => 'ID de producto no válido.']);
        }
    }
}

// --- Enrutador de Acciones ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    // No se necesita instanciar el controlador aquí si la verificación está en el constructor
    // Pero para llamar al método, sí.
    $controller = new ProductoController();

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => "Acción no encontrada"]);
    }
}
?>