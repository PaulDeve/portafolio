<?php
// controllers/AuthController.php

// Iniciar la sesión en la parte superior de tu script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

    public function login() {
        // Verificar si el formulario fue enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Sanitizar y obtener datos del formulario
            $username = trim(htmlspecialchars($_POST['username']));
            $password = trim(htmlspecialchars($_POST['password']));

            // 2. Validar datos (básico)
            if (empty($username) || empty($password)) {
                $this->redirectWithErrors('../views/auth/login.php', 'El usuario y la contraseña son obligatorios.');
                return;
            }

            // 3. Verificar credenciales
            $usuarioModel = new Usuario();
            $user = $usuarioModel->findUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // 4. Credenciales correctas: Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['rol_nombre'];

                // Actualizar última sesión
                $usuarioModel->updateLastSession($user['id']);

                // 5. Redirigir según el rol
                $this->redirectUserByRole($user['rol_nombre']);
            } else {
                // Credenciales incorrectas
                $this->redirectWithErrors('../views/auth/login.php', 'Usuario o contraseña incorrectos.');
            }
        } else {
            // Si no es POST, simplemente mostrar el login
            header('Location: ../views/auth/login.php');
            exit;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ../../views/auth/login.php?logout=success');
        exit;
    }

    private function redirectUserByRole($role) {
        switch (strtolower($role)) {
            case 'administrador':
                header('Location: ../../views/admin/dashboard.php');
                break;
            case 'vendedor':
                header('Location: ../../views/vendedor/venta.php');
                break;
            case 'recepcionista':
                header('Location: ../../views/recepcionista/clientes.php');
                break;
            default:
                // Rol no reconocido, redirigir a login
                header('Location: ../../views/auth/login.php');
                break;
        }
        exit;
    }

    private function redirectWithErrors($location, $message) {
        $_SESSION['error_message'] = $message;
        header("Location: $location");
        exit;
    }
}

// Manejo de la acción solicitada (enrutador simple)
if (isset($_GET['action'])) {
    $controller = new AuthController();
    $action = $_GET['action'];

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        // Acción no válida
        http_response_code(404);
        echo "Acción no encontrada";
    }
}
