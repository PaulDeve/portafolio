<?php
/* Diagnostic script for login issues.
   Run in browser: http://localhost/Ferreteria/scripts/debug_login.php
   It will show DB connection, list users, and test password_verify for common test passwords.
*/
require_once __DIR__ . '/../config/conexion.php';
if (!isset($conexion_global)) {
    require_once __DIR__ . '/../config/conexion.php';
}
echo '<h2>Debug login</h2>';
try {
    $db = $conexion_global->getPdo();
    echo '<p>Conexión a la base de datos: OK</p>';
    $stmt = $db->query('SELECT id_usuario, nombre_usuario, usuario, contrasena, rol FROM usuarios');
    $users = $stmt->fetchAll();
    if (!$users) {
        echo '<p>No hay usuarios en la tabla <strong>usuarios</strong>.</p>';
        exit;
    }
    echo '<table border="1" cellpadding="6"><tr><th>id</th><th>usuario</th><th>nombre</th><th>rol</th><th>hash</th></tr>';
    foreach($users as $u) {
        echo '<tr><td>'.htmlspecialchars($u['id_usuario']).'</td><td>'.htmlspecialchars($u['usuario']).'</td><td>'.htmlspecialchars($u['nombre_usuario']).'</td><td>'.htmlspecialchars($u['rol']).'</td><td style="font-family:monospace">'.htmlspecialchars($u['contrasena']).'</td></tr>';
    }
    echo '</table>';

    // Test password_verify for common test passwords
    $tests = ['admin123','vendedor123','recepcion123'];
    echo '<h3>Pruebas de password_verify</h3>';
    foreach($users as $u) {
        echo '<h4>User: '.htmlspecialchars($u['usuario']).'</h4>';
        foreach($tests as $t) {
            $ok = password_verify($t, $u['contrasena']) ? 'OK' : 'NO';
            echo '<div>'.htmlspecialchars($t).' => <strong>'.$ok.'</strong></div>';
        }
    }

} catch (Exception $e) {
    echo '<p style="color:red">Error: '.htmlspecialchars($e->getMessage()).'</p>';
}

echo '<p>Después de comprobar pega aquí la salida para que te ayude a interpretar.</p>';
