<?php
require_once __DIR__ . '/db.php';
$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'];
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($method === 'OPTIONS') exit;

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM boletas WHERE id = :id');
        $stmt->execute([':id' => $_GET['id']]);
        $b = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($b && isset($b['items'])) $b['items'] = json_decode($b['items'], true);
        json_response($b ?: null);
    } else {
        $stmt = $pdo->query('SELECT * FROM boletas ORDER BY fecha DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) if (isset($r['items'])) $r['items'] = json_decode($r['items'], true);
        json_response($rows);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) json_response(['error' => 'invalid json']);
    if (!empty($data['id'])) {
        $sql = 'UPDATE boletas SET pacienteId=:pacienteId, paciente=:paciente, items=:items, total=:total, fecha=:fecha WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pacienteId' => $data['pacienteId'] ?? null,
            ':paciente' => $data['paciente'] ?? null,
            ':items' => isset($data['items']) ? json_encode($data['items']) : null,
            ':total' => $data['total'] ?? 0,
            ':fecha' => $data['fecha'] ?? date('c'),
            ':id' => $data['id']
        ]);
        json_response(['ok' => true]);
    }
    $sql = 'INSERT INTO boletas (pacienteId, paciente, items, total, fecha) VALUES (:pacienteId, :paciente, :items, :total, :fecha)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pacienteId' => $data['pacienteId'] ?? null,
        ':paciente' => $data['paciente'] ?? null,
        ':items' => isset($data['items']) ? json_encode($data['items']) : null,
        ':total' => $data['total'] ?? 0,
        ':fecha' => $data['fecha'] ?? date('c')
    ]);
    json_response(['ok' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'DELETE') {
    if (!isset($_GET['id'])) json_response(['error' => 'id required']);
    $stmt = $pdo->prepare('DELETE FROM boletas WHERE id = :id');
    $stmt->execute([':id' => $_GET['id']]);
    json_response(['ok' => true]);
}

http_response_code(405);
json_response(['error' => 'method not allowed']);
