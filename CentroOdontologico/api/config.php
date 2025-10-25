<?php
require_once __DIR__ . '/db.php';
$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'];
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($method === 'OPTIONS') exit;

if ($method === 'GET') {
    $stmt = $pdo->query('SELECT value FROM kv WHERE key = "config" LIMIT 1');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $val = $row ? json_decode($row['value'], true) : ['nombreClinica' => 'Clínica Dental', 'mostrarNotificaciones' => true];
    json_response($val);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) json_response(['error' => 'invalid json']);
    $stmt = $pdo->prepare('REPLACE INTO kv (key, value) VALUES (:k, :v)');
    $stmt->execute([':k' => 'config', ':v' => json_encode($data)]);
    json_response(['ok' => true]);
}

http_response_code(405); json_response(['error' => 'method not allowed']);
