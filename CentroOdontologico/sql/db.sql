-- Esquema mínimo para OdHCL
-- Está pensado para sqlite (XAMPP: puede usar sqlite o migrar a MySQL cambiando tipos y claves)

BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS pacientes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT,
    apellido TEXT,
    fechaNacimiento TEXT,
    edad INTEGER,
    sexo TEXT,
    telefono TEXT,
    dni TEXT,
    email TEXT,
    antecedentes TEXT,
    extraoral TEXT,
    intraoral TEXT,
    tratamiento TEXT,
    evolucion TEXT,
    odontograma TEXT,
    historia INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS citas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    paciente TEXT,
    pacienteId INTEGER,
    fecha TEXT,
    hora TEXT,
    notas TEXT,
    FOREIGN KEY(pacienteId) REFERENCES pacientes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS boletas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pacienteId INTEGER,
    paciente TEXT,
    items TEXT,
    total NUMERIC,
    fecha TEXT,
    FOREIGN KEY(pacienteId) REFERENCES pacientes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS kv (
    key TEXT PRIMARY KEY,
    value TEXT
);

COMMIT;
