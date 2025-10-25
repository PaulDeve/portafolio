<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/csrf.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$db = $conexion_global->getPdo();
require_once __DIR__ . '/../config/roles.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/ventas.php'); exit;
}

// CSRF
if (!csrf_check($_POST['csrf_token'] ?? '')) {
    $_SESSION['error'] = 'Token CSRF inválido';
    header('Location: ../views/ventas.php'); exit;
}

$id_cliente = $_POST['id_cliente'] ?: null;
$id_usuario = $_SESSION['id_usuario'] ?? null;
$productos = $_POST['productos'] ?? [];
$precios = $_POST['precios'] ?? [];
$cantidades = $_POST['cantidades'] ?? [];

// Datos de cliente manual
$cliente_nombre = trim($_POST['cliente_nombre'] ?? '');
$cliente_dni = trim($_POST['cliente_dni'] ?? '');
$cliente_telefono = trim($_POST['cliente_telefono'] ?? '');
$cliente_direccion = trim($_POST['cliente_direccion'] ?? '');

if (!can_sell()) { $_SESSION['error']='Permisos insuficientes para registrar ventas'; header('Location: ../views/ventas.php'); exit; }
if (count($productos) === 0) {
    $_SESSION['error'] = 'No se proporcionaron productos';
    header('Location: ../views/ventas.php'); exit;
}

try {
    $db->beginTransaction();
    $total = 0;
    for ($i=0;$i<count($productos);$i++) {
        $p = intval($productos[$i]);
        $precio = floatval($precios[$i]);
        $cant = intval($cantidades[$i]);
        $total += $precio * $cant;
        // verificar stock
        $stmt = $db->prepare('SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE');
        $stmt->execute([$p]);
        $row = $stmt->fetch();
        if (!$row) throw new Exception('Producto no encontrado');
        if ($row['stock'] < $cant) throw new Exception('Stock insuficiente para el producto ID '.$p);
    }
    // insertar venta
    // Si no se pasó id_cliente, intentar buscar/crear cliente por DNI o nombre
    if (empty($id_cliente)) {
        if ($cliente_dni) {
            $s = $db->prepare('SELECT id_cliente FROM clientes WHERE dni = ? LIMIT 1');
            $s->execute([$cliente_dni]);
            $found = $s->fetch();
            if ($found) $id_cliente = $found['id_cliente'];
        }
        if (empty($id_cliente)) {
            // crear cliente nuevo
            $ins = $db->prepare('INSERT INTO clientes (nombre,dni,telefono,direccion) VALUES (?,?,?,?)');
            $ins->execute([$cliente_nombre ?: 'Consumidor Final',$cliente_dni,$cliente_telefono,$cliente_direccion]);
            $id_cliente = $db->lastInsertId();
        }
    }

    $stmt = $db->prepare('INSERT INTO ventas (id_cliente,id_usuario,total) VALUES (?,?,?)');
    $stmt->execute([$id_cliente,$id_usuario,$total]);
    $id_venta = $db->lastInsertId();
    // insertar detalles y descontar stock
    for ($i=0;$i<count($productos);$i++) {
        $p = intval($productos[$i]);
        $precio = floatval($precios[$i]);
        $cant = intval($cantidades[$i]);
        $subtotal = $precio * $cant;
        $stmt = $db->prepare('INSERT INTO detalle_venta (id_venta,id_producto,cantidad,subtotal) VALUES (?,?,?,?)');
        $stmt->execute([$id_venta,$p,$cant,$subtotal]);
        $stmt = $db->prepare('UPDATE productos SET stock = stock - ? WHERE id_producto = ?');
        $stmt->execute([$cant,$p]);
    }
    $db->commit();
    $_SESSION['success'] = 'Venta registrada correctamente';
    header('Location: ../views/ventas.php'); exit;
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'] = 'Error al registrar venta: '.$e->getMessage();
    header('Location: ../views/ventas.php'); exit;
}
