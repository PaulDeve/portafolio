<?php
require_once __DIR__ . '/db.php';

$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'];

// permitimos CORS local (útil al usar XAMPP y desarrollo)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($method === 'OPTIONS') exit;

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE id = :id');
        $stmt->execute([':id' => $_GET['id']]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response($p ?: null);
    } else {
        $stmt = $pdo->query('SELECT * FROM pacientes ORDER BY apellido, nombre');
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response($list);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) json_response(['error' => 'invalid json']);
    // si viene id -> update
    if (!empty($data['id'])) {
        $sql = 'UPDATE pacientes SET nombre=:nombre, apellido=:apellido, fechaNacimiento=:fechaNacimiento, edad=:edad, sexo=:sexo, telefono=:telefono, dni=:dni, email=:email, antecedentes=:antecedentes, extraoral=:extraoral, intraoral=:intraoral, tratamiento=:tratamiento, evolucion=:evolucion, odontograma=:odontograma, historia=:historia WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':apellido' => $data['apellido'] ?? '',
            ':fechaNacimiento' => $data['fechaNacimiento'] ?? null,
            ':edad' => $data['edad'] ?? null,
            ':sexo' => $data['sexo'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':dni' => $data['dni'] ?? null,
            ':email' => $data['email'] ?? null,
            ':antecedentes' => $data['antecedentes'] ?? null,
            ':extraoral' => $data['extraoral'] ?? null,
            ':intraoral' => $data['intraoral'] ?? null,
            ':tratamiento' => $data['tratamiento'] ?? null,
            ':evolucion' => $data['evolucion'] ?? null,
            ':odontograma' => isset($data['odontograma']) ? json_encode($data['odontograma']) : null,
            ':historia' => !empty($data['historia']) ? 1 : 0,
            ':id' => $data['id']
        ]);
        json_response(['ok' => true]);
    }
    // crear nuevo paciente
    $sql = 'INSERT INTO pacientes (nombre, apellido, fechaNacimiento, edad, sexo, telefono, dni, email, antecedentes, extraoral, intraoral, tratamiento, evolucion, odontograma, historia) VALUES (:nombre,:apellido,:fechaNacimiento,:edad,:sexo,:telefono,:dni,:email,:antecedentes,:extraoral,:intraoral,:tratamiento,:evolucion,:odontograma,:historia)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $data['nombre'] ?? '',
        ':apellido' => $data['apellido'] ?? '',
        ':fechaNacimiento' => $data['fechaNacimiento'] ?? null,
        ':edad' => $data['edad'] ?? null,
        ':sexo' => $data['sexo'] ?? null,
        ':telefono' => $data['telefono'] ?? null,
        ':dni' => $data['dni'] ?? null,
        ':email' => $data['email'] ?? null,
        ':antecedentes' => $data['antecedentes'] ?? null,
        ':extraoral' => $data['extraoral'] ?? null,
        ':intraoral' => $data['intraoral'] ?? null,
        ':tratamiento' => $data['tratamiento'] ?? null,
        ':evolucion' => $data['evolucion'] ?? null,
        ':odontograma' => isset($data['odontograma']) ? json_encode($data['odontograma']) : null,
        ':historia' => !empty($data['historia']) ? 1 : 0
    ]);
    json_response(['ok' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'DELETE') {
    // espera ?id=...
    if (!isset($_GET['id'])) json_response(['error' => 'id required']);
    $stmt = $pdo->prepare('DELETE FROM pacientes WHERE id = :id');
    $stmt->execute([':id' => $_GET['id']]);
    // eliminar citas y boletas relacionadas (cascade debería manejarlo si FK set)
    json_response(['ok' => true]);
}

http_response_code(405);
json_response(['error' => 'method not allowed']);
