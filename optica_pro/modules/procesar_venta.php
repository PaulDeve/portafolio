<?php
session_start();
require_once '../config/db.php';

// 1. Verificaciones de seguridad y de estado
// ¿El usuario está logueado? ¿Es una petición POST? ¿El carrito está vacío? ¿Se seleccionó un cliente?
if (
    !isset($_SESSION['id_usuario']) ||
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_SESSION['carrito']) ||
    empty($_POST['id_cliente'])
) {
    // Si algo falla, redirigimos a la página de ventas con un error.
    header('Location: ventas.php?status=error');
    exit();
}

// 2. Recopilar datos
$id_cliente = $_POST['id_cliente'];
$id_usuario = $_SESSION['id_usuario'];
$carrito = $_SESSION['carrito'];
$fecha = date('Y-m-d');

// 3. Calcular el total de la venta
$total_venta = 0;
foreach ($carrito as $item) {
    $total_venta += $item['precio'] * $item['cantidad'];
}

// 4. Iniciar una transacción para asegurar la integridad de los datos
// Esto garantiza que todas las operaciones (insertar venta, detalles, actualizar stock) se completen
// o ninguna lo haga. Evita inconsistencias en la base de datos.
$pdo->beginTransaction();

try {
    // 4.1. Insertar la venta en la tabla 'ventas'
    $stmtVenta = $pdo->prepare(
        "INSERT INTO ventas (id_cliente, id_usuario, fecha, total) VALUES (?, ?, ?, ?)"
    );
    $stmtVenta->execute([$id_cliente, $id_usuario, $fecha, $total_venta]);

    // 4.2. Obtener el ID de la venta recién creada
    $id_venta = $pdo->lastInsertId();

    // 4.3. Preparar las consultas para los detalles y la actualización de stock
    $stmtDetalle = $pdo->prepare(
        "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)"
    );
    $stmtStock = $pdo->prepare(
        "UPDATE productos SET stock = stock - ? WHERE id_producto = ?"
    );

    // 4.4. Recorrer el carrito para insertar los detalles y actualizar el stock
    foreach ($carrito as $id_producto => $item) {
        $subtotal = $item['precio'] * $item['cantidad'];
        $stmtDetalle->execute([$id_venta, $id_producto, $item['cantidad'], $subtotal]);
        $stmtStock->execute([$item['cantidad'], $id_producto]);
    }

    // 4.5. Si todo fue bien, confirmamos la transacción
    $pdo->commit();

    // 5. Limpiar el carrito y redirigir al comprobante de pago
    $_SESSION['carrito'] = [];
    // Redirigir al comprobante con el id de la venta para que el usuario pueda imprimirlo
    header('Location: comprobante.php?id=' . $id_venta);
    exit();

} catch (Exception $e) {
    // 6. Si algo falló, revertimos todos los cambios
    $pdo->rollBack();
    // Y redirigimos con un mensaje de error, registrando el error si es necesario.
    // error_log($e->getMessage()); // Opcional: para depuración
    header('Location: ventas.php?status=dberror');
    exit();
}