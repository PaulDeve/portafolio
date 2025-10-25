<?php
// db.php - Conexión PDO. Intenta conectar a MySQL (XAMPP) por defecto;
// si falla, hace fallback a SQLite local en data/odhcl.sqlite.
// Ajusta las credenciales MySQL más abajo si es necesario.

function json_response($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function get_db() {
    static $pdo = null;
    if ($pdo) return $pdo;

    // CONFIG: cambia estos valores si tu MySQL usa otras credenciales
    $mysql = [
        'host' => '127.0.0.1',
        'db'   => 'odhcl',
        'user' => 'root',
        'pass' => ''
    ];

    // Intentar MySQL primero (XAMPP)
    try {
        $dsn = "mysql:host={$mysql['host']};dbname={$mysql['db']};charset=utf8mb4";
        $pdo = new PDO($dsn, $mysql['user'], $mysql['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (Exception $e) {
        // Si falla MySQL, continuar a SQLite fallback (útil en desarrollo)
        error_log('MySQL connect failed, falling back to SQLite: ' . $e->getMessage());
    }

    // Fallback: SQLite
    try {
        $dir = __DIR__ . '/../data';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $file = $dir . '/odhcl.sqlite';
        $dsn = "sqlite:" . $file;
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // habilitar FK en sqlite
        $pdo->exec('PRAGMA foreign_keys = ON;');

        // Si la DB está vacía, intentar aplicar el esquema SQL provisto
        $schemaFile = __DIR__ . '/../sql/db.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            try { $pdo->exec($sql); } catch (Exception $ex) { /* silencioso */ }
        }
        return $pdo;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'DB connection failed', 'detail' => $e->getMessage()]);
        exit;
    }
}
