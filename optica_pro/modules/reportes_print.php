<?php
require_once '../includes/header.php';
require_once '../config/db.php';

$ventas_mes = $pdo->query("SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as total_ventas, SUM(total) as ingresos FROM ventas GROUP BY mes ORDER BY mes DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
$top_productos = $pdo->query("SELECT p.nombre, SUM(dv.cantidad) as total_vendido FROM detalle_venta dv JOIN productos p ON dv.id_producto = p.id_producto GROUP BY p.id_producto ORDER BY total_vendido DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container py-4 comprobante">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Reportes - OpticaPro</h4>
                <small class="text-muted">Generado: <?php echo date('Y-m-d H:i'); ?></small>
            </div>
            <div class="text-end">
                <button class="btn btn-outline-secondary" onclick="window.print()">Imprimir / Guardar PDF</button>
            </div>
        </div>

        <h5>Ingresos por Mes</h5>
        <table class="table table-striped">
            <thead>
                <tr><th>Mes</th><th>Total Ventas</th><th>Ingresos (S/)</th></tr>
            </thead>
            <tbody>
                <?php foreach ($ventas_mes as $v): ?>
                <tr>
                    <td><?php echo htmlspecialchars($v['mes']); ?></td>
                    <td><?php echo htmlspecialchars($v['total_ventas']); ?></td>
                    <td><?php echo number_format($v['ingresos'],2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h5>Productos Más Vendidos</h5>
        <table class="table table-striped">
            <thead>
                <tr><th>Producto</th><th>Total Vendido</th></tr>
            </thead>
            <tbody>
                <?php foreach ($top_productos as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($p['total_vendido']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
