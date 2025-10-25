-- SQL adaptado para MySQL / MariaDB
CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255),
    apellido VARCHAR(255),
    fechaNacimiento DATE,
    edad INT,
    sexo VARCHAR(10),
    telefono VARCHAR(60),
    dni VARCHAR(60),
    email VARCHAR(255),
    antecedentes TEXT,
    extraoral TEXT,
    intraoral TEXT,
    tratamiento TEXT,
    evolucion TEXT,
    odontograma TEXT,
    historia TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente VARCHAR(255),
    pacienteId INT,
    fecha DATE,
    hora VARCHAR(10),
    notas TEXT,
    FOREIGN KEY (pacienteId) REFERENCES pacientes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS boletas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pacienteId INT,
    paciente VARCHAR(255),
    items TEXT,
    total DECIMAL(10,2),
    fecha DATETIME,
    FOREIGN KEY (pacienteId) REFERENCES pacientes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kv (
    `key` VARCHAR(128) PRIMARY KEY,
    `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
