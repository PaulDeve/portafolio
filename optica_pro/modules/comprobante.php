<?php
require_once '../includes/header.php';
require_once '../config/db.php';

$id_venta = $_GET['id'] ?? null;
if (!$id_venta) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>ID de venta no proporcionado.</div></div>";
    include '../includes/footer.php';
    exit();
}

// Obtener datos de la venta
$stmt = $pdo->prepare("SELECT v.*, c.nombre as cliente_nombre, c.dni as cliente_dni, c.correo as cliente_correo, u.nombre as usuario_nombre
                       FROM ventas v
                       JOIN clientes c ON v.id_cliente = c.id_cliente
                       JOIN usuarios u ON v.id_usuario = u.id_usuario
                       WHERE v.id_venta = ?");
$stmt->execute([$id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Venta no encontrada.</div></div>";
    include '../includes/footer.php';
    exit();
}

// Detalle de la venta
$stmtDet = $pdo->prepare("SELECT dv.*, p.nombre as producto_nombre FROM detalle_venta dv JOIN productos p ON dv.id_producto = p.id_producto WHERE dv.id_venta = ?");
$stmtDet->execute([$id_venta]);
$detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

// Datos del sistema (pueden venir de configuración o hardcodeados)
$sistema_nombre = 'OpticaPro';
$sistema_direccion = 'Av. Ejemplo 123';
$sistema_ruc = '20501234567';
$sistema_telefono = '(01) 234-5678';

?>

<div class="container comprobante py-4">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><?php echo htmlspecialchars($sistema_nombre); ?></h4>
                <small class="text-muted"><?php echo htmlspecialchars($sistema_direccion); ?> | RUC: <?php echo $sistema_ruc; ?></small>
            </div>
            <div class="text-end">
                <h6 class="mb-0">Comprobante de Pago</h6>
                <small class="text-muted">Venta #<?php echo $id_venta; ?></small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Cliente</strong>
                <p class="mb-0"><?php echo htmlspecialchars($venta['cliente_nombre']); ?></p>
                <small class="text-muted">DNI: <?php echo htmlspecialchars($venta['cliente_dni']); ?> | <?php echo htmlspecialchars($venta['cliente_correo']); ?></small>
            </div>
            <div class="col-md-6 text-end">
                <strong>Atendido por</strong>
                <p class="mb-0"><?php echo htmlspecialchars($venta['usuario_nombre']); ?></p>
                <small class="text-muted">Fecha: <?php echo htmlspecialchars($venta['fecha']); ?></small>
            </div>
        </div>

        <div class="table-responsive mb-3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $d): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d['producto_nombre']); ?></td>
                        <td class="text-center"><?php echo $d['cantidad']; ?></td>
                        <td class="text-end">S/ <?php echo number_format($d['subtotal'] / $d['cantidad'],2); ?></td>
                        <td class="text-end">S/ <?php echo number_format($d['subtotal'],2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td class="text-end"><strong>S/ <?php echo number_format($venta['total'],2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">Gracias por su compra.</small>
            </div>
            <div>
                <button class="btn btn-outline-secondary" onclick="window.print()">Imprimir</button>
                <a href="ventas.php" class="btn btn-primary ms-2">Volver a Ventas</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
