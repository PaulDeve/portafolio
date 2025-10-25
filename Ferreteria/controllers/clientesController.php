<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$db = $conexion_global->getPdo();
require_once __DIR__ . '/../config/roles.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Token CSRF inválido'; header('Location: ../views/clientes.php'); exit;
    }
    $id = $_POST['id_cliente'] ?: null;
    $nombre = $_POST['nombre'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    if (!$nombre) { $_SESSION['error']='Nombre requerido'; header('Location: ../views/clientes.php'); exit; }
    if ($id) {
        if (!is_recepcion() && !is_admin()) { $_SESSION['error']='Permisos insuficientes'; header('Location: ../views/clientes.php'); exit; }
        $stmt = $db->prepare('UPDATE clientes SET nombre=?, dni=?, telefono=?, direccion=? WHERE id_cliente=?');
        $stmt->execute([$nombre,$dni,$telefono,$direccion,$id]);
        $_SESSION['success']='Cliente actualizado';
    } else {
        $stmt = $db->prepare('INSERT INTO clientes (nombre,dni,telefono,direccion) VALUES (?,?,?,?)');
        $stmt->execute([$nombre,$dni,$telefono,$direccion]);
        $_SESSION['success']='Cliente creado';
    }
    header('Location: ../views/clientes.php'); exit;
}

if (isset($_GET['action']) && $_GET['action']==='delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $db->prepare('DELETE FROM clientes WHERE id_cliente = ?');
        $stmt->execute([$id]);
        $_SESSION['success']='Cliente eliminado';
    }
    header('Location: ../views/clientes.php'); exit;
}
