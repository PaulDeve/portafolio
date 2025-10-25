<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$db = $conexion_global->getPdo();
require_once __DIR__ . '/../config/roles.php';

$action = $_REQUEST['action'] ?? 'save';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json'); echo json_encode(['error'=>'Token CSRF inválido']); exit;
        }
        $_SESSION['error'] = 'Token CSRF inválido';
        header('Location: ../views/productos.php'); exit;
    }
    // Guardar o actualizar
    $id = $_POST['id_producto'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $precio_compra = $_POST['precio_compra'] ?? 0;
    $precio_venta = $_POST['precio_venta'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $proveedor_id = $_POST['proveedor_id'] ?? null;

    if (!$nombre) {
        $_SESSION['error'] = 'El nombre es requerido';
        header('Location: ../views/producto_form.php' . ($id?"?id={$id}":'')); exit;
    }

    if (!can_manage_products()) { 
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') { header('Content-Type: application/json'); echo json_encode(['error'=>'Permisos insuficientes']); exit; }
        $_SESSION['error'] = 'Permisos insuficientes'; header('Location: ../views/productos.php'); exit;
    }
    if ($id) {
        $stmt = $db->prepare('UPDATE productos SET nombre=?, descripcion=?, categoria=?, precio_compra=?, precio_venta=?, stock=?, proveedor_id=? WHERE id_producto=?');
        $stmt->execute([$nombre,$descripcion,$categoria,$precio_compra,$precio_venta,$stock,$proveedor_id,$id]);
        $msg = 'Producto actualizado';
    } else {
        $stmt = $db->prepare('INSERT INTO productos (nombre,descripcion,categoria,precio_compra,precio_venta,stock,proveedor_id) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$nombre,$descripcion,$categoria,$precio_compra,$precio_venta,$stock,$proveedor_id]);
        $msg = 'Producto creado';
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json'); echo json_encode(['success'=>$msg]); exit;
    }
    $_SESSION['success'] = $msg;
    header('Location: ../views/productos.php'); exit;
}

if ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $stmt = $db->prepare('DELETE FROM productos WHERE id_producto = ?');
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Producto eliminado';
    }
    header('Location: ../views/productos.php'); exit;
}
