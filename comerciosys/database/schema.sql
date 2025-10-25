-- Script de creación de base de datos para ComercioSys
-- Sistema de Gestión de Ventas

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS comerciosys_db;
USE comerciosys_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_cod INT PRIMARY KEY,
    apellido VARCHAR(50) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    nick VARCHAR(50) UNIQUE NOT NULL,
    pass VARCHAR(50) NOT NULL,
    rol ENUM('Administrador', 'Soporte', 'Caja') NOT NULL
);

-- Tabla de cobranza (ventas)
CREATE TABLE cobranza (
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

-- Datos iniciales de usuarios
INSERT INTO usuarios (id_cod, apellido, nombre, nick, pass, rol) VALUES 
(101, 'Cuentas', 'Omar', 'user1', '12345', 'Soporte'), 
(102, 'Gallegos', 'Nestor', 'user2', '1234', 'Caja'), 
(103, 'Neira', 'Ruben', 'Admin', '123456', 'Administrador');

-- Datos iniciales de ventas
INSERT INTO cobranza (id_cod_usuario, concepto, fecha, hora, cantidad, precio_unitario, total) VALUES
(102, 'Televisor 38°', '2025-10-02', '13:15:00', 2, 650.00, 1300.00),
(102, 'Equipo de sonido', '2025-10-02', '13:16:00', 1, 1500.00, 1500.00),
(102, 'Microondas', '2025-10-02', '14:45:00', 2, 450.00, 900.00);

-- Verificar datos insertados
SELECT 'Base de datos creada exitosamente' as mensaje;
SELECT COUNT(*) as total_usuarios FROM usuarios;
SELECT COUNT(*) as total_ventas FROM cobranza;
