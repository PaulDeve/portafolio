-- Crear tabla de compradores/clientes
-- ComercioSys - Sistema de Gestión de Ventas

CREATE TABLE IF NOT EXISTS compradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apellido VARCHAR(50) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Insertar algunos compradores de ejemplo
INSERT IGNORE INTO compradores (apellido, nombre, telefono, email, direccion) VALUES 
('García', 'María', '555-0101', 'maria.garcia@email.com', 'Av. Principal 123, Ciudad'),
('López', 'Carlos', '555-0102', 'carlos.lopez@email.com', 'Calle Secundaria 456, Ciudad'),
('Martínez', 'Ana', '555-0103', 'ana.martinez@email.com', 'Plaza Central 789, Ciudad');
