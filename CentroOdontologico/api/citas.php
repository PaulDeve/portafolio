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
        $stmt = $pdo->prepare('SELECT * FROM citas WHERE id = :id');
        $stmt->execute([':id' => $_GET['id']]);
        $c = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response($c ?: null);
    } else {
        $stmt = $pdo->query('SELECT * FROM citas ORDER BY fecha, hora');
        json_response($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) json_response(['error' => 'invalid json']);
    if (!empty($data['id'])) {
        $sql = 'UPDATE citas SET paciente=:paciente, pacienteId=:pacienteId, fecha=:fecha, hora=:hora, notas=:notas WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':paciente' => $data['paciente'] ?? null,
            ':pacienteId' => $data['pacienteId'] ?? null,
            ':fecha' => $data['fecha'] ?? null,
            ':hora' => $data['hora'] ?? null,
            ':notas' => $data['notas'] ?? null,
            ':id' => $data['id']
        ]);
        json_response(['ok' => true]);
    }
    $sql = 'INSERT INTO citas (paciente, pacienteId, fecha, hora, notas) VALUES (:paciente, :pacienteId, :fecha, :hora, :notas)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':paciente' => $data['paciente'] ?? null,
        ':pacienteId' => $data['pacienteId'] ?? null,
        ':fecha' => $data['fecha'] ?? null,
        ':hora' => $data['hora'] ?? null,
        ':notas' => $data['notas'] ?? null
    ]);
    json_response(['ok' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'DELETE') {
    if (!isset($_GET['id'])) json_response(['error' => 'id required']);
    $stmt = $pdo->prepare('DELETE FROM citas WHERE id = :id');
    $stmt->execute([':id' => $_GET['id']]);
    json_response(['ok' => true]);
}

http_response_code(405);
json_response(['error' => 'method not allowed']);
