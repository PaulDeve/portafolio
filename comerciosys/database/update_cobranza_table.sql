-- Actualizar tabla cobranza para usar compradores en lugar de usuarios
-- ComercioSys - Sistema de Gestión de Ventas

-- Eliminar la restricción de clave foránea existente
ALTER TABLE cobranza DROP FOREIGN KEY cobranza_ibfk_1;

-- Cambiar el nombre de la columna para que sea más claro
ALTER TABLE cobranza CHANGE COLUMN id_cod_usuario id_comprador INT;

-- Agregar nueva restricción de clave foránea hacia compradores
ALTER TABLE cobranza ADD CONSTRAINT fk_cobranza_comprador 
    FOREIGN KEY (id_comprador) REFERENCES compradores(id) ON DELETE CASCADE;
