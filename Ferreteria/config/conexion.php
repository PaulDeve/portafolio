<?php
// config/conexion.php
// Conexión a la base de datos usando PDO
session_start();

class Conexion {
    private $host = '127.0.0.1';
    private $db   = 'ferreteria_db';
    private $user = 'root';
    private $pass = ''; // por defecto en XAMPP
    private $charset = 'utf8mb4';
    public $pdo;

    public function __construct(){
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $opt);
        } catch (PDOException $e) {
            // Mensaje amigable en caso de error
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    public function getPdo(){
        return $this->pdo;
    }
}

// Instancia global opcional
$conexion_global = new Conexion();
?>
