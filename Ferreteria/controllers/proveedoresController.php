<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$db = $conexion_global->getPdo();
require_once __DIR__ . '/../config/roles.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) { $_SESSION['error']='Token CSRF inválido'; header('Location: ../views/proveedores.php'); exit; }
    $id = $_POST['id_proveedor'] ?: null;
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    if (!$nombre) { $_SESSION['error']='Nombre requerido'; header('Location: ../views/proveedores.php'); exit; }
    if ($id) {
        if (!is_recepcion() && !is_admin()) { $_SESSION['error']='Permisos insuficientes'; header('Location: ../views/proveedores.php'); exit; }
        $stmt = $db->prepare('UPDATE proveedores SET nombre=?, telefono=?, direccion=? WHERE id_proveedor=?');
        $stmt->execute([$nombre,$telefono,$direccion,$id]);
        $_SESSION['success']='Proveedor actualizado';
    } else {
        if (!is_recepcion() && !is_admin()) { $_SESSION['error']='Permisos insuficientes'; header('Location: ../views/proveedores.php'); exit; }
        $stmt = $db->prepare('INSERT INTO proveedores (nombre,telefono,direccion) VALUES (?,?,?)');
        $stmt->execute([$nombre,$telefono,$direccion]);
        $_SESSION['success']='Proveedor creado';
    }
    header('Location: ../views/proveedores.php'); exit;
}

if (isset($_GET['action']) && $_GET['action']==='delete') {
    $id = $_GET['id'] ?? null;
    if ($id) { $stmt = $db->prepare('DELETE FROM proveedores WHERE id_proveedor = ?'); $stmt->execute([$id]); $_SESSION['success']='Proveedor eliminado'; }
    header('Location: ../views/proveedores.php'); exit;
}
