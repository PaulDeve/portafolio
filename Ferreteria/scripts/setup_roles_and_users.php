<?php
/*
  scripts/setup_roles_and_users.php
  Ejecuta este script desde navegador local para crear dos usuarios de prueba:
  - vendedor / vendedor123 (rol: vendedor)
  - recepcion / recepcion123 (rol: recepcion)
  También intenta añadir los valores 'vendedor' y 'recepcion' al enum de la tabla usuarios si no existen.
  BORRA este archivo después de usarlo.
*/
require_once __DIR__ . '/../../Ferreteria/config/conexion.php';
if (!isset($conexion_global)) require_once __DIR__ . '/../config/conexion.php';
if (!isset($conexion_global)) die('No se pudo cargar la conexión.');
$db = $conexion_global->getPdo();
try {
    // Verificar columna rol
    $row = $db->query("SHOW COLUMNS FROM usuarios LIKE 'rol'")->fetch();
    $type = $row['Type'] ?? '';
    if (strpos($type,'enum(') !== false) {
        if (strpos($type, "'vendedor'") === false || strpos($type, "'recepcion'") === false) {
            // reconstruir enum con los valores necesarios
            $vals = [ 'admin','empleado','vendedor','recepcion' ];
            $vals_sql = "'" . implode("','", $vals) . "'";
            $db->exec("ALTER TABLE usuarios MODIFY COLUMN rol ENUM($vals_sql) NOT NULL DEFAULT 'empleado'");
            echo "Enum de roles actualizado.<br>";
        }
    }
    // Crear usuarios si no existen
    $users = [
      ['nombre'=>'Vendedor','usuario'=>'vendedor','pass'=>'vendedor123','rol'=>'vendedor'],
      ['nombre'=>'Recepcion','usuario'=>'recepcion','pass'=>'recepcion123','rol'=>'recepcion']
    ];
    foreach($users as $u) {
      $stmt = $db->prepare('SELECT id_usuario FROM usuarios WHERE usuario = ?');
      $stmt->execute([$u['usuario']]);
      if (!$stmt->fetch()) {
        $hash = password_hash($u['pass'], PASSWORD_DEFAULT);
        $ins = $db->prepare('INSERT INTO usuarios (nombre_usuario,usuario,contrasena,rol) VALUES (?,?,?,?)');
        $ins->execute([$u['nombre'],$u['usuario'],$hash,$u['rol']]);
        echo "Usuario {$u['usuario']} creado con contraseña {$u['pass']}<br>";
      } else {
        echo "Usuario {$u['usuario']} ya existe<br>";
      }
    }
    echo "Listo. Borra este archivo por seguridad.";
} catch (Exception $e) { echo 'Error: '.$e->getMessage(); }
