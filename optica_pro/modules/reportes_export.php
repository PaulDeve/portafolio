<?php
require_once '../config/db.php';

$type = $_GET['type'] ?? 'excel';

// Query same data as reportes
$ventas_mes = $pdo->query("SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as total_ventas, SUM(total) as ingresos FROM ventas GROUP BY mes ORDER BY mes DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
$top_productos = $pdo->query("SELECT p.nombre, SUM(dv.cantidad) as total_vendido FROM detalle_venta dv JOIN productos p ON dv.id_producto = p.id_producto GROUP BY p.id_producto ORDER BY total_vendido DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

if ($type === 'excel') {
    // Build CSV content
    $filename = 'reportes_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');
    // UTF-8 BOM to help Excel detect encoding
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Section: Ingresos por mes
    fputcsv($output, ['Ingresos por Mes']);
    fputcsv($output, ['Mes', 'Total Ventas', 'Ingresos']);
    foreach ($ventas_mes as $v) {
        fputcsv($output, [$v['mes'], $v['total_ventas'], $v['ingresos']]);
    }
    fputcsv($output, []);

    // Section: Productos mas vendidos
    fputcsv($output, ['Productos Más Vendidos']);
    fputcsv($output, ['Producto', 'Total Vendido']);
    foreach ($top_productos as $p) {
        fputcsv($output, [$p['nombre'], $p['total_vendido']]);
    }

    fclose($output);
    exit();
}

// fallback: redirect back
header('Location: reportes.php');
exit();
