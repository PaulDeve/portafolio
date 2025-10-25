<?php
/**
 * Archivo de conexión a la base de datos
 * ComercioSys - Sistema de Gestión de Ventas
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'comerciosys_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            // Verificar conexión
            if ($this->conn->connect_error) {
                die("Error de conexión: " . $this->conn->connect_error);
            }
            
            // Establecer charset
            $this->conn->set_charset("utf8");
            
        } catch(Exception $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}

// Función helper para obtener conexión
function getDBConnection() {
    $database = new Database();
    return $database->getConnection();
}
?>
