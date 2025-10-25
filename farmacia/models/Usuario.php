<?php
// models/Usuario.php

require_once __DIR__ . '/Model.php';

class Usuario extends Model {
    
    /**
     * Busca un usuario por su nombre de usuario y devuelve sus datos junto con el nombre de su rol.
     *
     * @param string $username El nombre de usuario a buscar.
     * @return array|false Los datos del usuario y su rol, o false si no se encuentra.
     */
    public function findUserByUsername(string $username) {
        $sql = "SELECT u.*, r.nombre as rol_nombre 
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                WHERE u.usuario = :username AND u.estado = 1";
        
        $stmt = $this->query($sql, [':username' => $username]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza la última hora de sesión de un usuario.
     *
     * @param int $id El ID del usuario.
     * @return void
     */
    public function updateLastSession(int $id): void {
        $sql = "UPDATE usuarios SET ultima_sesion = NOW() WHERE id = :id";
        $this->query($sql, [':id' => $id]);
    }

    // Aquí se podrían agregar otros métodos para el CRUD de usuarios (crear, leer, actualizar, eliminar)
}
?>