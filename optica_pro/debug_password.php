<?php
// Archivo: debug_password.php
// Colócalo en la carpeta raíz de tu proyecto (optica_pro)
// y ábrelo en el navegador: http://localhost/optica/optica_pro/debug_password.php

require_once 'config/db.php';

echo "<h1>Iniciando depuración de contraseña...</h1>";

try {
    // 1. Buscar al usuario 'admin'
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<p style='color: red; font-weight: bold;'>ERROR: No se encontró al usuario 'admin' en la base de datos.</p>";
        exit();
    }

    echo "<p>Usuario 'admin' encontrado.</p>";

    // 2. Mostrar la contraseña ALMACENADA en la base de datos
    $stored_password = $user['contrasena'];
    echo "<p>Contraseña guardada en la BD: </p>";
    echo "<pre style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc;'>" . htmlspecialchars($stored_password) . "</pre>";

    // 3. Verificar si la contraseña '1234' coincide con la almacenada
    $password_to_check = '1234';
    $is_match = password_verify($password_to_check, $stored_password);

    echo "<p>Verificando si '1234' coincide con la contraseña guardada...</p>";

    if ($is_match) {
        echo "<p style='color: green; font-weight: bold;'>¡ÉXITO! La contraseña '1234' SÍ coincide. El login debería funcionar.</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>¡FALLO! La contraseña '1234' NO coincide. Esto explica el error.</p>";
        echo "<p><b>Causa probable:</b> La contraseña en la base de datos no es un hash válido o es un hash de una contraseña diferente. Probablemente sigue siendo '1234' en texto plano.</p>";
        echo "<p><b>Solución:</b> Vuelve a importar el archivo <code>optica_pro.sql</code> para asegurarte de que el usuario 'admin' tenga el hash correcto.</p>";
    }

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>
