<?php
/**
 * Sistema de autenticación
 * ComercioSys - Sistema de Gestión de Ventas
 */

session_start();

// Verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Verificar rol de usuario
function hasRole($required_role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['user_role'] === $required_role;
}

// Verificar si es administrador
function isAdmin() {
    return hasRole('Administrador');
}

// Obtener datos del usuario actual
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'nombre' => $_SESSION['user_name'],
        'apellido' => $_SESSION['user_apellido'],
        'nick' => $_SESSION['user_nick'],
        'rol' => $_SESSION['user_role']
    ];
}

// Función de login
function login($nick, $password) {
    require_once __DIR__ . '/../database/connection.php';
    
    $conn = getDBConnection();
    
    // Consulta preparada para evitar SQL injection
    $stmt = $conn->prepare("SELECT id_cod, apellido, nombre, nick, pass, rol FROM usuarios WHERE nick = ?");
    $stmt->bind_param("s", $nick);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña (sin hash por especificación)
        if ($user['pass'] === $password) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id_cod'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_apellido'] = $user['apellido'];
            $_SESSION['user_nick'] = $user['nick'];
            $_SESSION['user_role'] = $user['rol'];
            
            $stmt->close();
            $conn->close();
            return true;
        }
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

// Función de logout
function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Redirigir si no está logueado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Redirigir si no es administrador
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit();
    }
}
?>
