<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$db = $conexion_global->getPdo();

// Solo admin puede eliminar o cambiar roles de otros usuarios
function is_admin(){ return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) { $_SESSION['error']='Token CSRF inválido'; header('Location: ../views/usuarios.php'); exit; }
    $id = $_POST['id_usuario'] ?: null;
    $nombre = $_POST['nombre_usuario'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $rol = $_POST['rol'] ?? 'empleado';
    if (!$nombre || !$usuario) { $_SESSION['error']='Nombre y usuario son requeridos'; header('Location: ../views/usuarios.php'); exit; }
    if ($id) {
        // Si no es admin y intenta editar rol o eliminar alguno, evitar
        if (!is_admin()) {
            // evitar cambiar rol
            $stmt = $db->prepare('UPDATE usuarios SET nombre_usuario=?, usuario=? WHERE id_usuario=?');
            $stmt->execute([$nombre,$usuario,$id]);
        } else {
            if ($contrasena) {
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $stmt = $db->prepare('UPDATE usuarios SET nombre_usuario=?, usuario=?, contrasena=?, rol=? WHERE id_usuario=?');
                $stmt->execute([$nombre,$usuario,$hash,$rol,$id]);
            } else {
                $stmt = $db->prepare('UPDATE usuarios SET nombre_usuario=?, usuario=?, rol=? WHERE id_usuario=?');
                $stmt->execute([$nombre,$usuario,$rol,$id]);
            }
        }
        $_SESSION['success']='Usuario actualizado';
    } else {
        // crear
        if (!$contrasena) { $_SESSION['error']='Contraseña requerida'; header('Location: ../views/usuarios.php'); exit; }
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO usuarios (nombre_usuario,usuario,contrasena,rol) VALUES (?,?,?,?)');
        $stmt->execute([$nombre,$usuario,$hash,$rol]);
        $_SESSION['success']='Usuario creado';
    }
    header('Location: ../views/usuarios.php'); exit;
}

if (isset($_GET['action']) && $_GET['action']==='delete') {
    $id = $_GET['id'] ?? null;
    if (!$id) { header('Location: ../views/usuarios.php'); exit; }
    if (!is_admin()) { $_SESSION['error']='Solo administradores pueden eliminar usuarios'; header('Location: ../views/usuarios.php'); exit; }
    // evitar borrar al propio admin si último
    $stmt = $db->prepare('DELETE FROM usuarios WHERE id_usuario = ?');
    $stmt->execute([$id]);
    $_SESSION['success']='Usuario eliminado';
    header('Location: ../views/usuarios.php'); exit;
}
