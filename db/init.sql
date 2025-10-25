-- init.sql: crea bases de datos para cada proyecto y un usuario demo
CREATE DATABASE IF NOT EXISTS centroodontologico CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE IF NOT EXISTS comerciosys CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE IF NOT EXISTS farmacia CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE IF NOT EXISTS ferreteria CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE IF NOT EXISTS gestionrs CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE IF NOT EXISTS optica_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- crea usuario demo (cambia contraseña en producción)
CREATE USER IF NOT EXISTS 'demo'@'%' IDENTIFIED BY 'demo';
GRANT ALL PRIVILEGES ON centroodontologico.* TO 'demo'@'%';
GRANT ALL PRIVILEGES ON comerciosys.* TO 'demo'@'%';
GRANT ALL PRIVILEGES ON farmacia.* TO 'demo'@'%';
GRANT ALL PRIVILEGES ON ferreteria.* TO 'demo'@'%';
GRANT ALL PRIVILEGES ON gestionrs.* TO 'demo'@'%';
GRANT ALL PRIVILEGES ON optica_pro.* TO 'demo'@'%';
FLUSH PRIVILEGES;
