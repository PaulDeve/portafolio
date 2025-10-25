<?php
// config/db.php

class Database {
    private $host = 'localhost';
    private $db_name = 'farmacia_db';
    private $username = 'root'; // Usuario por defecto en XAMPP
    private $password = ''; // Contraseña por defecto en XAMPP
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8', $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            // En un entorno de producción, no mostrarías el error directamente.
            // Lo registrarías en un archivo de log.
            error_log('Error de conexión: ' . $exception->getMessage());
            // Para este proyecto, mostramos un mensaje genérico.
            die('Error de conexión a la base de datos. Por favor, revise la configuración en config/db.php y asegúrese de que el servidor MySQL esté en ejecución.');
        }

        return $this->conn;
    }
}
?>