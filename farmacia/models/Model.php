<?php
require_once __DIR__ . '/../config/db.php';

class Model {
    protected $db;
    protected $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    protected function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            $query_type = strtoupper(substr(trim($sql), 0, 6));
            if (in_array($query_type, ['INSERT', 'UPDATE', 'DELETE'])) {
                $rowCount = $stmt->rowCount();
                $logMessage = date('[Y-m-d H:i:s]') . " [EXECUTE] SQL: $sql | Params: " . json_encode($params) . " | RowCount: $rowCount\n";
                file_put_contents(__DIR__ . '/../sql.log', $logMessage, FILE_APPEND);
            }

            return $stmt;
        } catch (PDOException $e) {
            $logMessage = date('[Y-m-d H:i:s]') . " [ERROR] " . $e->getMessage() . " | SQL: $sql | Params: " . json_encode($params) . "\n";
            file_put_contents(__DIR__ . '/../sql.log', $logMessage, FILE_APPEND);
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }
}
?>