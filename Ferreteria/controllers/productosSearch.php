<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$db = $conexion_global->getPdo();
$q = $_GET['q'] ?? '';
$limit = intval($_GET['limit'] ?? 20);
if ($q) {
  $stmt = $db->prepare('SELECT id_producto,nombre,categoria,precio_venta,stock FROM productos WHERE nombre LIKE ? OR categoria LIKE ? ORDER BY nombre LIMIT ?');
  $stmt->execute(["%$q%","%$q%", $limit]);
} else {
  $stmt = $db->prepare('SELECT id_producto,nombre,categoria,precio_venta,stock FROM productos ORDER BY nombre LIMIT ?');
  $stmt->execute([$limit]);
}
$rows = $stmt->fetchAll();
header('Content-Type: application/json'); echo json_encode($rows);
