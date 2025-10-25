<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new Cliente();
    }

    public function listar() {
        try {
            $clientes = $this->model->listar();
            return [
                'status' => 'success',
                'data' => $clientes
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function guardar($datos) {
        try {
            // Validaciones
            if (empty($datos['nombre'])) {
                throw new Exception('El nombre es requerido');
            }

            if (!empty($datos['dni'])) {
                // Verificar DNI único
                $existente = $this->model->buscarPorDni($datos['dni']);
                if ($existente && (!isset($datos['id']) || $existente['id'] != $datos['id'])) {
                    throw new Exception('El DNI ya está registrado');
                }
            }

            if (!empty($datos['email'])) {
                if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('El email no es válido');
                }
            }

            // Crear o actualizar
            if (!empty($datos['id'])) {
                $stmt = $this->model->actualizar($datos['id'], $datos);
                if ($stmt->rowCount() > 0) {
                    $mensaje = 'Cliente actualizado correctamente';
                } else {
                    throw new Exception('No se pudo actualizar el cliente. Verifique el ID o que los datos sean diferentes.');
                }
            } else {
                $stmt = $this->model->crear($datos);
                if ($stmt->rowCount() > 0) {
                    $mensaje = 'Cliente registrado correctamente';
                } else {
                    throw new Exception('No se pudo registrar el cliente.');
                }
            }

            return [
                'status' => 'success',
                'message' => $mensaje
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function eliminar($id) {
        try {
            $this->model->eliminar($id);
            return [
                'status' => 'success',
                'message' => 'Cliente eliminado correctamente'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function obtener($id) {
        try {
            $cliente = $this->model->obtener($id);
            if (!$cliente) {
                throw new Exception('Cliente no encontrado');
            }
            return [
                'status' => 'success',
                'data' => $cliente
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function buscar($termino) {
        try {
            $clientes = $this->model->buscarPorNombre($termino);
            return [
                'status' => 'success',
                'data' => $clientes
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function obtenerHistorial($id) {
        try {
            $historial = $this->model->obtenerHistorialCompras($id);
            return [
                'status' => 'success',
                'data' => $historial
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['PHP_SELF']) === 'ClienteController.php') {
    header('Content-Type: application/json');
    $controller = new ClienteController();
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'crear':
        case 'actualizar':
            echo json_encode($controller->guardar($_POST));
            break;
        case 'eliminar':
            echo json_encode($controller->eliminar($_POST['id']));
            break;
        case 'obtener':
            echo json_encode($controller->obtener($_POST['id']));
            break;
        case 'buscar':
            echo json_encode($controller->buscar($_POST['termino']));
            break;
        case 'historial':
            echo json_encode($controller->obtenerHistorial($_POST['id']));
            break;
        case 'listar':
            echo json_encode($controller->listar());
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    }
}
?>