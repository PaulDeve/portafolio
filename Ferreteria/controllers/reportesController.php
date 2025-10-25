<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$db = $conexion_global->getPdo();

$format = $_GET['format'] ?? '';
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;
$where = [];
$params = [];
if ($desde) { $where[] = 'fecha >= ?'; $params[] = $desde.' 00:00:00'; }
if ($hasta) { $where[] = 'fecha <= ?'; $params[] = $hasta.' 23:59:59'; }
$whereSql = $where ? 'WHERE '.implode(' AND ', $where) : '';

if ($format==='csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reportes_'.date('Ymd_His').'.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id_venta','fecha','total']);
    $stmt = $db->prepare("SELECT id_venta,fecha,total FROM ventas $whereSql ORDER BY fecha DESC");
    $stmt->execute($params);
    while ($row = $stmt->fetch()) { fputcsv($out, [$row['id_venta'],$row['fecha'],$row['total']]); }
    fclose($out);
    exit;
}

// else redirect back
header('Location: ../views/reportes.php'); exit;
