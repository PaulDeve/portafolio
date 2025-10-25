<?php
/**
 * Verificador del sistema ComercioSys
 * Sistema de Gestión de Ventas
 */

// Configuración
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'comerciosys_db'
];

$checks = [];
$errors = [];
$warnings = [];

// Función para agregar resultado de verificación
function addCheck($name, $status, $message, $type = 'info') {
    global $checks;
    $checks[] = [
        'name' => $name,
        'status' => $status,
        'message' => $message,
        'type' => $type
    ];
}

// Verificar PHP
$php_version = PHP_VERSION;
if (version_compare($php_version, '8.0.0', '>=')) {
    addCheck('PHP Version', true, "PHP $php_version (Recomendado: 8.0+)", 'success');
} else {
    addCheck('PHP Version', false, "PHP $php_version (Recomendado: 8.0+)", 'error');
    $errors[] = "Versión de PHP no compatible";
}

// Verificar extensiones PHP
$required_extensions = ['mysqli', 'session', 'json', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        addCheck("PHP Extension: $ext", true, "Extensión $ext disponible", 'success');
    } else {
        addCheck("PHP Extension: $ext", false, "Extensión $ext no encontrada", 'error');
        $errors[] = "Extensión PHP requerida: $ext";
    }
}

// Verificar conexión a MySQL
try {
    $conn = new mysqli($db_config['host'], $db_config['username'], $db_config['password']);
    if ($conn->connect_error) {
        addCheck('MySQL Connection', false, "Error de conexión: " . $conn->connect_error, 'error');
        $errors[] = "No se puede conectar a MySQL";
    } else {
        addCheck('MySQL Connection', true, "Conexión a MySQL exitosa", 'success');
        $conn->close();
    }
} catch (Exception $e) {
    addCheck('MySQL Connection', false, "Error: " . $e->getMessage(), 'error');
    $errors[] = "Error de conexión a MySQL";
}

// Verificar base de datos
try {
    $conn = new mysqli($db_config['host'], $db_config['username'], $db_config['password'], $db_config['database']);
    if ($conn->connect_error) {
        addCheck('Database', false, "Base de datos no encontrada", 'error');
        $errors[] = "Base de datos no existe";
    } else {
        addCheck('Database', true, "Base de datos '{$db_config['database']}' encontrada", 'success');
        
        // Verificar tablas
        $tables = ['usuarios', 'cobranza'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                addCheck("Table: $table", true, "Tabla '$table' existe", 'success');
            } else {
                addCheck("Table: $table", false, "Tabla '$table' no encontrada", 'error');
                $errors[] = "Tabla requerida no encontrada: $table";
            }
        }
        
        // Verificar datos
        $user_count = $conn->query("SELECT COUNT(*) as count FROM usuarios")->fetch_assoc()['count'];
        $venta_count = $conn->query("SELECT COUNT(*) as count FROM cobranza")->fetch_assoc()['count'];
        
        if ($user_count > 0) {
            addCheck('Users Data', true, "$user_count usuarios encontrados", 'success');
        } else {
            addCheck('Users Data', false, "No hay usuarios en la base de datos", 'warning');
            $warnings[] = "No hay usuarios registrados";
        }
        
        if ($venta_count > 0) {
            addCheck('Sales Data', true, "$venta_count ventas encontradas", 'success');
        } else {
            addCheck('Sales Data', false, "No hay ventas en la base de datos", 'info');
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    addCheck('Database', false, "Error: " . $e->getMessage(), 'error');
    $errors[] = "Error al acceder a la base de datos";
}

// Verificar archivos del sistema
$required_files = [
    'database/connection.php' => 'Conexión a base de datos',
    'includes/auth.php' => 'Sistema de autenticación',
    'includes/functions.php' => 'Funciones auxiliares',
    'login.php' => 'Página de login',
    'dashboard.php' => 'Panel principal',
    'ventas.php' => 'Módulo de ventas',
    'usuarios.php' => 'Módulo de usuarios',
    'css/style.css' => 'Estilos del sistema',
    'js/script.js' => 'JavaScript del sistema'
];

foreach ($required_files as $file => $description) {
    if (file_exists($file)) {
        addCheck("File: $file", true, "$description encontrado", 'success');
    } else {
        addCheck("File: $file", false, "$description no encontrado", 'error');
        $errors[] = "Archivo requerido no encontrado: $file";
    }
}

// Verificar directorios
$directories = ['logs', 'backups', 'uploads'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            addCheck("Directory: $dir", true, "Directorio '$dir' existe y es escribible", 'success');
        } else {
            addCheck("Directory: $dir", false, "Directorio '$dir' existe pero no es escribible", 'warning');
            $warnings[] = "Directorio '$dir' no tiene permisos de escritura";
        }
    } else {
        addCheck("Directory: $dir", false, "Directorio '$dir' no existe", 'warning');
        $warnings[] = "Directorio requerido no existe: $dir";
    }
}

// Verificar permisos de archivos
$writable_files = ['logs/', 'backups/', 'uploads/'];
foreach ($writable_files as $file) {
    if (file_exists($file)) {
        if (is_writable($file)) {
            addCheck("Permissions: $file", true, "Permisos de escritura OK", 'success');
        } else {
            addCheck("Permissions: $file", false, "Sin permisos de escritura", 'error');
            $errors[] = "Sin permisos de escritura en: $file";
        }
    }
}

// Verificar configuración de sesiones
if (ini_get('session.auto_start') == 0) {
    addCheck('Session Auto Start', true, "session.auto_start está deshabilitado (correcto)", 'success');
} else {
    addCheck('Session Auto Start', false, "session.auto_start está habilitado (recomendado: deshabilitado)", 'warning');
    $warnings[] = "session.auto_start debería estar deshabilitado";
}

// Verificar configuración de errores
if (ini_get('display_errors') == 0) {
    addCheck('Display Errors', true, "display_errors está deshabilitado (producción)", 'success');
} else {
    addCheck('Display Errors', false, "display_errors está habilitado (solo para desarrollo)", 'warning');
    $warnings[] = "display_errors debería estar deshabilitado en producción";
}

// Calcular estado general
$total_checks = count($checks);
$success_checks = count(array_filter($checks, function($check) { return $check['status']; }));
$error_checks = count($errors);
$warning_checks = count($warnings);

$overall_status = 'success';
if ($error_checks > 0) {
    $overall_status = 'error';
} elseif ($warning_checks > 0) {
    $overall_status = 'warning';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾 ComercioSys - Verificación del Sistema</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .check-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .check-summary {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            gap: 1rem;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .check-icon {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }
        .check-icon.success {
            background: #10b981;
            color: white;
        }
        .check-icon.error {
            background: #ef4444;
            color: white;
        }
        .check-icon.warning {
            background: #f59e0b;
            color: white;
        }
        .check-icon.info {
            background: #3b82f6;
            color: white;
        }
        .check-content {
            flex: 1;
        }
        .check-name {
            font-weight: 600;
            color: #1e293b;
        }
        .check-message {
            color: #64748b;
            font-size: 0.875rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        .status-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        .status-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
    </style>
</head>
<body>
    <div class="check-container">
        <div class="check-summary">
            <h1>🧾 ComercioSys - Verificación del Sistema</h1>
            
            <div class="stats-grid" style="margin: 2rem 0;">
                <div class="stat-card">
                    <div class="stat-icon" style="background: <?php echo $overall_status === 'success' ? '#10b981' : ($overall_status === 'warning' ? '#f59e0b' : '#ef4444'); ?>;">
                        <i class="fas fa-<?php echo $overall_status === 'success' ? 'check-circle' : ($overall_status === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $success_checks; ?>/<?php echo $total_checks; ?></h3>
                        <p>Verificaciones Exitosas</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #ef4444;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $error_checks; ?></h3>
                        <p>Errores</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f59e0b;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $warning_checks; ?></h3>
                        <p>Advertencias</p>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-<?php echo $overall_status === 'success' ? 'success' : ($overall_status === 'warning' ? 'warning' : 'error'); ?>">
                <i class="fas fa-<?php echo $overall_status === 'success' ? 'check-circle' : ($overall_status === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                <strong>
                    <?php if ($overall_status === 'success'): ?>
                        Sistema funcionando correctamente
                    <?php elseif ($overall_status === 'warning'): ?>
                        Sistema funcionando con advertencias
                    <?php else: ?>
                        Sistema con errores críticos
                    <?php endif; ?>
                </strong>
            </div>
            
            <div style="margin-top: 2rem;">
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Ir al Sistema
                </a>
                <a href="install.php" class="btn btn-outline">
                    <i class="fas fa-cog"></i>
                    Instalador
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list-check"></i> Detalles de Verificación</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <?php foreach ($checks as $check): ?>
                    <div class="check-item">
                        <div class="check-icon <?php echo $check['type']; ?>">
                            <i class="fas fa-<?php echo $check['status'] ? 'check' : ($check['type'] === 'warning' ? 'exclamation' : 'times'); ?>"></i>
                        </div>
                        <div class="check-content">
                            <div class="check-name"><?php echo htmlspecialchars($check['name']); ?></div>
                            <div class="check-message"><?php echo htmlspecialchars($check['message']); ?></div>
                        </div>
                        <div class="status-badge status-<?php echo $check['type']; ?>">
                            <?php echo ucfirst($check['type']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3 style="color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Errores Críticos</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($warnings)): ?>
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3 style="color: #f59e0b;"><i class="fas fa-exclamation-triangle"></i> Advertencias</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <?php foreach ($warnings as $warning): ?>
                            <li><?php echo htmlspecialchars($warning); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
