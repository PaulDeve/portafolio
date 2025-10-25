-- Corregir datos en cobranza para que coincidan con compradores
-- ComercioSys - Sistema de Gestión de Ventas

-- Crear compradores con IDs que coincidan con los datos existentes
INSERT IGNORE INTO compradores (id, apellido, nombre, telefono, email) VALUES 
(102, 'Gallegos', 'Nestor', '555-0002', 'nestor.gallegos@email.com');

-- Ahora agregar la restricción de clave foránea
ALTER TABLE cobranza ADD CONSTRAINT fk_cobranza_comprador 
    FOREIGN KEY (id_comprador) REFERENCES compradores(id) ON DELETE CASCADE;
