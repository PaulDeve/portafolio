<?php
// Script de inicialización: crea la base de datos y tablas en MySQL
// Úsalo solo una vez desde el navegador: http://localhost/.../api/init_mysql.php

// Configuración (ajusta si tu MySQL usa otras credenciales)
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbName = 'odhcl';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    // Conectar a la base
    $pdo->exec("USE `$dbName`;");
    // Cargar archivo SQL si existe
    $sqlFile = __DIR__ . '/../sql/db_mysql.sql';
    if (!file_exists($sqlFile)) {
        echo json_encode(['error' => 'sql/db_mysql.sql not found']); exit;
    }
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    echo json_encode(['ok' => true, 'msg' => 'Database initialized (odhcl)']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
