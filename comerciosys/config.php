<?php
/**
 * Archivo de configuración del sistema
 * ComercioSys - Sistema de Gestión de Ventas
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'comerciosys_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración del sistema
define('SITE_NAME', 'ComercioSys');
define('SITE_VERSION', '1.0.0');
define('SITE_URL', 'http://localhost/comerciosys');

// Configuración de sesiones
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Configuración de archivos
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Configuración de paginación
define('ITEMS_PER_PAGE', 10);

// Configuración de moneda
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_DECIMALS', 2);

// Configuración de fecha y hora
define('DATE_FORMAT', 'd/m/Y');
define('TIME_FORMAT', 'H:i');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// Configuración de roles
define('ROLES', [
    'Administrador' => 'Administrador',
    'Soporte' => 'Soporte', 
    'Caja' => 'Caja'
]);

// Configuración de colores del sistema
define('THEME_COLORS', [
    'primary' => '#2563eb',
    'secondary' => '#64748b',
    'success' => '#10b981',
    'warning' => '#f59e0b',
    'error' => '#ef4444'
]);

// Configuración de desarrollo
define('DEBUG_MODE', true);
define('SHOW_ERRORS', true);

// Configuración de logs
define('LOG_ERRORS', true);
define('LOG_FILE', 'logs/error.log');

// Configuración de backup
define('BACKUP_ENABLED', true);
define('BACKUP_DIR', 'backups/');

// Configuración de reportes
define('REPORT_FORMATS', ['pdf', 'excel', 'csv']);
define('DEFAULT_REPORT_FORMAT', 'pdf');

// Configuración de notificaciones
define('EMAIL_NOTIFICATIONS', false);
define('EMAIL_FROM', 'noreply@comerciosys.com');

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 4);
define('LOGIN_ATTEMPTS_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos

// Configuración de backup automático
define('AUTO_BACKUP', false);
define('BACKUP_FREQUENCY', 'daily'); // daily, weekly, monthly

// Configuración de idioma
define('DEFAULT_LANGUAGE', 'es');
define('SUPPORTED_LANGUAGES', ['es', 'en']);

// Configuración de zona horaria
date_default_timezone_set('America/Lima');

// Configuración de memoria
ini_set('memory_limit', '128M');
ini_set('max_execution_time', 300);

// Configuración de errores
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Función para obtener configuración
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Función para validar configuración
function validateConfig() {
    $errors = [];
    
    // Verificar conexión a base de datos
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            $errors[] = "Error de conexión a la base de datos: " . $conn->connect_error;
        }
        $conn->close();
    } catch (Exception $e) {
        $errors[] = "Error al conectar con la base de datos: " . $e->getMessage();
    }
    
    // Verificar directorios necesarios
    $directories = ['logs', 'backups', 'uploads'];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $errors[] = "No se pudo crear el directorio: $dir";
            }
        }
    }
    
    return $errors;
}

// Función para obtener información del sistema
function getSystemInfo() {
    return [
        'name' => SITE_NAME,
        'version' => SITE_VERSION,
        'url' => SITE_URL,
        'php_version' => PHP_VERSION,
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'database' => DB_NAME,
        'timezone' => date_default_timezone_get(),
        'debug_mode' => DEBUG_MODE
    ];
}
?>
