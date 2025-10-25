<?php
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Producto.php';

class VentaController {
    private $ventaModel;
    private $productoModel;

    public function __construct() {
        $this->ventaModel = new Venta();
        $this->productoModel = new Producto();
    }

    public function registrarVenta($datos) {
        try {
            // Validar stock suficiente
            foreach ($datos['productos'] as $producto) {
                $stockActual = $this->productoModel->obtener($producto['id'])['stock'];
                if ($stockActual < $producto['cantidad']) {
                    throw new Exception("Stock insuficiente para el producto: " . $producto['nombre']);
                }
            }

            // Generar código único
            $datos['codigo'] = $this->ventaModel->generarCodigo();
            
            // Calcular totales
            $subtotal = 0;
            foreach ($datos['productos'] as &$producto) {
                // Asegurarse de que las claves existen antes de usarlas
                $precio_venta = $producto['precio_venta'] ?? 0;
                $cantidad = $producto['cantidad'] ?? 0;

                $producto['subtotal'] = $precio_venta * $cantidad;
                $subtotal += $producto['subtotal']; // Usar el subtotal ya calculado en el frontend es más seguro
            }
            
            $datos['subtotal'] = $subtotal;
            $datos['igv'] = $subtotal * 0.18;
            $datos['total'] = $subtotal + $datos['igv'];

            // Registrar venta
            $ventaId = $this->ventaModel->crear($datos);

            return [
                'status' => 'success',
                'message' => 'Venta registrada correctamente',
                'venta_id' => $ventaId
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function obtenerVenta($id) {
        try {
            $venta = $this->ventaModel->obtenerVenta($id);
            if (!$venta) {
                throw new Exception('Venta no encontrada');
            }
            return [
                'status' => 'success',
                'data' => $venta
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function listarVentas() {
        try {
            $ventas = $this->ventaModel->listarVentas();
            return [
                'status' => 'success',
                'data' => $ventas
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function buscarProducto($termino) {
        try {
            $productos = $this->productoModel->buscarPorNombre($termino);
            return [
                'status' => 'success',
                'data' => $productos
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}

// Manejar solicitudes AJAX
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['PHP_SELF']) == 'VentaController.php') {
    header('Content-Type: application/json');
    $controller = new VentaController();

    // Handle JSON input
    $datos = $_POST;
    if (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $json_data = file_get_contents('php://input');
        if ($json_data) {
            $datos = json_decode($json_data, true) ?? [];
        }
    }

    $action = $datos['action'] ?? '';

    switch ($action) {
        case 'registrar':
            echo json_encode($controller->registrarVenta($datos));
            break;
        case 'obtener':
            echo json_encode($controller->obtenerVenta($datos['id']));
            break;
        case 'listar':
            echo json_encode($controller->listarVentas());
            break;
        case 'buscar_producto':
            echo json_encode($controller->buscarProducto($datos['termino']));
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    }
    exit;
}
?>