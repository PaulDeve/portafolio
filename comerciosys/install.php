<?php
/**
 * Instalador automático del sistema ComercioSys
 * Sistema de Gestión de Ventas
 */

// Configuración de la base de datos
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'comerciosys_db'
];

$errors = [];
$success = [];

// Función para mostrar mensajes
function showMessage($type, $message) {
    $class = $type === 'error' ? 'alert-error' : 'alert-success';
    $icon = $type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
    echo "<div class='alert $class'><i class='fas $icon'></i> $message</div>";
}

// Función para probar conexión
function testConnection($config) {
    try {
        $conn = new mysqli($config['host'], $config['username'], $config['password']);
        if ($conn->connect_error) {
            return false;
        }
        $conn->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Función para crear base de datos
function createDatabase($config) {
    try {
        $conn = new mysqli($config['host'], $config['username'], $config['password']);
        
        $sql = "CREATE DATABASE IF NOT EXISTS {$config['database']}";
        if ($conn->query($sql) === TRUE) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

// Función para ejecutar script SQL
function executeSQL($config, $sql) {
    try {
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
        
        if ($conn->connect_error) {
            return false;
        }
        
        $conn->multi_query($sql);
        $conn->close();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Procesar instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    
    // 1. Probar conexión a MySQL
    if (!testConnection($db_config)) {
        $errors[] = "No se puede conectar a MySQL. Verifique que XAMPP esté ejecutándose.";
    } else {
        $success[] = "Conexión a MySQL exitosa.";
    }
    
    // 2. Crear base de datos
    if (empty($errors)) {
        if (createDatabase($db_config)) {
            $success[] = "Base de datos '{$db_config['database']}' creada exitosamente.";
        } else {
            $errors[] = "Error al crear la base de datos.";
        }
    }
    
    // 3. Crear tablas y datos
    if (empty($errors)) {
        $sql = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id_cod INT PRIMARY KEY,
            apellido VARCHAR(50) NOT NULL,
            nombre VARCHAR(50) NOT NULL,
            nick VARCHAR(50) UNIQUE NOT NULL,
            pass VARCHAR(50) NOT NULL,
            rol ENUM('Administrador', 'Soporte', 'Caja') NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS cobranza (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_cod_usuario INT,
            concepto VARCHAR(100) NOT NULL,
            fecha DATE NOT NULL,
            hora TIME NOT NULL,
            cantidad INT NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (id_cod_usuario) REFERENCES usuarios(id_cod)
        );
        
        INSERT IGNORE INTO usuarios (id_cod, apellido, nombre, nick, pass, rol) VALUES 
        (101, 'Cuentas', 'Omar', 'user1', '12345', 'Soporte'), 
        (102, 'Gallegos', 'Nestor', 'user2', '1234', 'Caja'), 
        (103, 'Neira', 'Ruben', 'Admin', '123456', 'Administrador');
        
        INSERT IGNORE INTO cobranza (id_cod_usuario, concepto, fecha, hora, cantidad, precio_unitario, total) VALUES
        (102, 'Televisor 38°', '2025-10-02', '13:15:00', 2, 650.00, 1300.00),
        (102, 'Equipo de sonido', '2025-10-02', '13:16:00', 1, 1500.00, 1500.00),
        (102, 'Microondas', '2025-10-02', '14:45:00', 2, 450.00, 900.00);
        ";
        
        if (executeSQL($db_config, $sql)) {
            $success[] = "Tablas y datos iniciales creados exitosamente.";
        } else {
            $errors[] = "Error al crear las tablas o insertar datos.";
        }
    }
    
    // 4. Crear directorios necesarios
    if (empty($errors)) {
        $directories = ['logs', 'backups', 'uploads'];
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                if (mkdir($dir, 0755, true)) {
                    $success[] = "Directorio '$dir' creado exitosamente.";
                } else {
                    $errors[] = "Error al crear el directorio '$dir'.";
                }
            }
        }
    }
    
    // 5. Verificar archivos del sistema
    if (empty($errors)) {
        $required_files = [
            'database/connection.php',
            'includes/auth.php',
            'includes/functions.php',
            'login.php',
            'dashboard.php',
            'ventas.php',
            'usuarios.php',
            'css/style.css',
            'js/script.js'
        ];
        
        foreach ($required_files as $file) {
            if (file_exists($file)) {
                $success[] = "Archivo '$file' encontrado.";
            } else {
                $errors[] = "Archivo requerido '$file' no encontrado.";
            }
        }
    }
    
    // 6. Verificar permisos
    if (empty($errors)) {
        $writable_dirs = ['logs', 'backups', 'uploads'];
        foreach ($writable_dirs as $dir) {
            if (is_writable($dir)) {
                $success[] = "Directorio '$dir' tiene permisos de escritura.";
            } else {
                $errors[] = "El directorio '$dir' no tiene permisos de escritura.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾 ComercioSys - Instalador</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .install-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .install-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .install-body {
            padding: 2rem;
        }
        .step {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }
        .step h3 {
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .requirements {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .requirement {
            padding: 1rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .requirement i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-card">
            <div class="install-header">
                <h1>🧾 ComercioSys</h1>
                <p>Instalador del Sistema de Gestión de Ventas</p>
            </div>
            
            <div class="install-body">
                <?php if (empty($errors) && !empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>¡Instalación completada exitosamente!</strong>
                        <p>El sistema ComercioSys ha sido instalado correctamente.</p>
                        <div style="margin-top: 1rem;">
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i>
                                Ir al Sistema
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Errores encontrados:</strong>
                        <ul style="margin-top: 0.5rem; padding-left: 1rem;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Progreso de la instalación:</strong>
                        <ul style="margin-top: 0.5rem; padding-left: 1rem;">
                            <?php foreach ($success as $msg): ?>
                                <li><?php echo htmlspecialchars($msg); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="step">
                    <h3><i class="fas fa-info-circle"></i> Requisitos del Sistema</h3>
                    <p>Antes de instalar, asegúrese de que cumple con los siguientes requisitos:</p>
                    
                    <div class="requirements">
                        <div class="requirement">
                            <i class="fas fa-server"></i>
                            <h4>XAMPP</h4>
                            <p>Apache y MySQL ejecutándose</p>
                        </div>
                        <div class="requirement">
                            <i class="fab fa-php"></i>
                            <h4>PHP 8+</h4>
                            <p>Versión 8.0 o superior</p>
                        </div>
                        <div class="requirement">
                            <i class="fas fa-database"></i>
                            <h4>MySQL</h4>
                            <p>Base de datos MySQL</p>
                        </div>
                        <div class="requirement">
                            <i class="fas fa-globe"></i>
                            <h4>Navegador</h4>
                            <p>Navegador web moderno</p>
                        </div>
                    </div>
                </div>
                
                <div class="step">
                    <h3><i class="fas fa-cog"></i> Configuración de la Base de Datos</h3>
                    <p>El instalador utilizará la siguiente configuración:</p>
                    <ul>
                        <li><strong>Host:</strong> localhost</li>
                        <li><strong>Usuario:</strong> root</li>
                        <li><strong>Contraseña:</strong> (vacía)</li>
                        <li><strong>Base de datos:</strong> comerciosys_db</li>
                    </ul>
                </div>
                
                <div class="step">
                    <h3><i class="fas fa-play"></i> Iniciar Instalación</h3>
                    <p>Haga clic en el botón para comenzar la instalación automática:</p>
                    
                    <form method="POST">
                        <button type="submit" name="install" class="btn btn-primary">
                            <i class="fas fa-download"></i>
                            Instalar ComercioSys
                        </button>
                    </form>
                </div>
                
                <div class="step">
                    <h3><i class="fas fa-users"></i> Usuarios Iniciales</h3>
                    <p>Después de la instalación, podrá usar estas credenciales:</p>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                        <p><strong>Administrador:</strong> Admin / 123456</p>
                        <p><strong>Soporte:</strong> user1 / 12345</p>
                        <p><strong>Caja:</strong> user2 / 1234</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
