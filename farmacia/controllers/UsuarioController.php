<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $model;

    public function __construct() {
        $this->model = new Usuario();
    }

    public function listar() {
        try {
            $usuarios = $this->model->listar();
            return [
                'status' => 'success',
                'data' => $usuarios
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
            if (empty($datos['nombre']) || empty($datos['usuario']) || empty($datos['rol'])) {
                throw new Exception('Los campos nombre, usuario y rol son requeridos');
            }

            // For creation, password is required
            if (empty($datos['id']) && empty($datos['password'])) {
                throw new Exception('La contraseña es requerida para nuevos usuarios');
            }

            // Validar email
            if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El formato del email no es válido');
            }

            // Crear o actualizar
            if (!empty($datos['id'])) {
                // Actualizar
                $this->model->actualizar($datos['id'], $datos['nombre'], $datos['email'], $datos['rol'], $datos['estado']);
                if (!empty($datos['password'])) {
                    $this->model->cambiarPassword($datos['id'], $datos['password']);
                }
                $mensaje = 'Usuario actualizado correctamente';
            } else {
                // Crear
                $this->model->crear($datos['nombre'], $datos['usuario'], $datos['password'], $datos['rol'], $datos['email'], $datos['estado']);
                $mensaje = 'Usuario creado correctamente';
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
                'message' => 'Usuario eliminado correctamente'
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
            $usuario = $this->model->obtener($id);
            if (!$usuario) {
                throw new Exception('Usuario no encontrado');
            }
            return [
                'status' => 'success',
                'data' => $usuario
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function cambiarPassword($id, $password) {
        try {
            if (strlen($password) < 6) {
                throw new Exception('La contraseña debe tener al menos 6 caracteres');
            }

            $this->model->cambiarPassword($id, $password);
            return [
                'status' => 'success',
                'message' => 'Contraseña actualizada correctamente'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function obtenerEstadisticas($id) {
        try {
            $stats = $this->model->obtenerEstadisticas($id);
            return [
                'status' => 'success',
                'data' => $stats
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['PHP_SELF']) === 'UsuarioController.php') {
    header('Content-Type: application/json');
    $controller = new UsuarioController();
    
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
        case 'cambiar_password':
            echo json_encode($controller->cambiarPassword($_POST['id'], $_POST['password']));
            break;
        case 'estadisticas':
            echo json_encode($controller->obtenerEstadisticas($_POST['id']));
            break;
        case 'listar':
            echo json_encode($controller->listar());
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    }
}
?>