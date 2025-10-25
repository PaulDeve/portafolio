-- Actualizar datos existentes en cobranza para usar compradores
-- ComercioSys - Sistema de Gestión de Ventas

-- Primero, eliminar la restricción de clave foránea existente
ALTER TABLE cobranza DROP FOREIGN KEY cobranza_ibfk_1;

-- Cambiar el nombre de la columna
ALTER TABLE cobranza CHANGE COLUMN id_cod_usuario id_comprador INT;

-- Actualizar los datos existentes para que apunten a compradores
-- Crear compradores para los usuarios existentes si no existen
INSERT IGNORE INTO compradores (id, apellido, nombre, telefono, email) VALUES 
(101, 'Cuentas', 'Omar', '555-0001', 'omar.cuentas@email.com'),
(102, 'Gallegos', 'Nestor', '555-0002', 'nestor.gallegos@email.com'),
(103, 'Neira', 'Ruben', '555-0003', 'ruben.neira@email.com');

-- Actualizar las ventas existentes para que apunten a los compradores
UPDATE cobranza SET id_comprador = id_comprador WHERE id_comprador IN (101, 102, 103);

-- Ahora agregar la nueva restricción de clave foránea
ALTER TABLE cobranza ADD CONSTRAINT fk_cobranza_comprador 
    FOREIGN KEY (id_comprador) REFERENCES compradores(id) ON DELETE CASCADE;
