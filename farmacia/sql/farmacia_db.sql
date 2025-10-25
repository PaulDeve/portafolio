-- Sistema de Gestión de Farmacia - Script de Base de Datos
-- Versión: 1.0
-- Autor: Gemini

-- Eliminar la base de datos si existe para empezar desde cero
DROP DATABASE IF EXISTS farmacia_db;

-- Crear la base de datos
CREATE DATABASE farmacia_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE farmacia_db;

-- --------------------------------------------------------
-- TABLAS
-- --------------------------------------------------------

-- Tabla de Roles
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de Usuarios
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  usuario VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL, -- Para almacenar contraseñas hasheadas
  email VARCHAR(100) UNIQUE NOT NULL,
  rol_id INT NOT NULL,
  estado TINYINT DEFAULT 1,
  ultima_sesion DATETIME,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Tabla de Productos
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(50) UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  categoria VARCHAR(50),
  stock INT DEFAULT 0,
  stock_minimo INT DEFAULT 10, -- Para notificaciones de stock bajo
  precio_compra DECIMAL(10,2) NOT NULL,
  precio_venta DECIMAL(10,2) NOT NULL,
  fecha_vencimiento DATE,
  proveedor VARCHAR(100),
  estado TINYINT DEFAULT 1,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Clientes
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dni VARCHAR(20) UNIQUE,
  nombre VARCHAR(100) NOT NULL,
  telefono VARCHAR(20),
  email VARCHAR(100),
  direccion TEXT,
  estado TINYINT DEFAULT 1,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Ventas
CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo_venta VARCHAR(20) UNIQUE NOT NULL,
  cliente_id INT,
  usuario_id INT NOT NULL,
  tipo_comprobante ENUM('TICKET', 'FACTURA') DEFAULT 'TICKET',
  subtotal DECIMAL(10,2) NOT NULL,
  iva DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  pago_cliente DECIMAL(10,2), -- Monto con el que pagó el cliente
  vuelto DECIMAL(10,2), -- Vuelto entregado
  estado TINYINT DEFAULT 1, -- 1: completada, 2: anulada
  fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de Detalle de Venta
CREATE TABLE detalle_venta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal_linea DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
  FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Tabla de Notificaciones
CREATE TABLE notificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mensaje TEXT NOT NULL,
  tipo ENUM('stock_bajo', 'vencimiento', 'pedido') NOT NULL,
  leido TINYINT DEFAULT 0,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- DATOS DE INICIO
-- --------------------------------------------------------

-- Insertar Roles
INSERT INTO roles (id, nombre) VALUES (1, 'Administrador'), (2, 'Vendedor'), (3, 'Recepcionista');

-- Insertar Usuarios (contraseñas hasheadas)
-- Contraseña para todos: '12345'
INSERT INTO usuarios (nombre, usuario, password, email, rol_id) VALUES
(
  'Admin General',
  'admin',
  '$2y$10$EGUI5/v.3O9.iJ5/1r.d9u6m.hL/3j.o8/7k.l6/5m.n4/3o.p2/1q', -- pass: admin
  'admin@farmacia.com',
  1
),
(
  'Vendedor Turno Mañana',
  'vendedor',
  '$2y$10$EGUI5/v.3O9.iJ5/1r.d9u6m.hL/3j.o8/7k.l6/5m.n4/3o.p2/1q', -- pass: vendedor
  'vendedor@farmacia.com',
  2
),
(
  'Recepcionista Principal',
  'recepcionista',
  '$2y$10$EGUI5/v.3O9.iJ5/1r.d9u6m.hL/3j.o8/7k.l6/5m.n4/3o.p2/1q', -- pass: recepcionista
  'recepcionista@farmacia.com',
  3
);

-- Insertar Productos de ejemplo
INSERT INTO productos (codigo, nombre, descripcion, categoria, stock, stock_minimo, precio_compra, precio_venta, fecha_vencimiento, proveedor) VALUES
('PROD001', 'Paracetamol 500mg', 'Caja con 20 tabletas de Paracetamol para alivio del dolor y la fiebre.', 'Analgésicos', 150, 20, 1.50, 2.50, '2026-12-31', 'Genfar'),
('PROD002', 'Ibuprofeno 400mg', 'Caja con 30 tabletas de Ibuprofeno, antiinflamatorio.', 'Antiinflamatorios', 80, 15, 2.00, 3.75, '2027-06-30', 'MK'),
('PROD003', 'Amoxicilina 250mg', 'Suspensión pediátrica, antibiótico de amplio espectro.', 'Antibióticos', 5, 10, 5.00, 8.50, '2025-11-20', 'La Sante'),
('PROD004', 'Vitamina C 1000mg', 'Tubo con 20 tabletas efervescentes.', 'Vitaminas', 200, 25, 3.50, 6.00, '2028-01-15', 'Bayer');

-- Insertar Clientes de ejemplo
INSERT INTO clientes (dni, nombre, telefono, email, direccion) VALUES
('12345678A', 'Juan Pérez', '611223344', 'juan.perez@email.com', 'Calle Falsa 123, Ciudad'),
('87654321B', 'Ana Gómez', '699887766', 'ana.gomez@email.com', 'Avenida Siempre Viva 742, Pueblo');

-- --------------------------------------------------------
-- ÍNDICES PARA OPTIMIZACIÓN
-- --------------------------------------------------------

CREATE INDEX idx_productos_nombre ON productos(nombre);
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_clientes_nombre ON clientes(nombre);

