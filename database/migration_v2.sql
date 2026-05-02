-- Migración v2 - FARMACIA ESEFJL
-- Actualización Crítica de Esquema

-- 1. Actualización de Productos
ALTER TABLE productos ADD COLUMN requiere_frio INTEGER DEFAULT 0;
ALTER TABLE productos ADD COLUMN es_delicado INTEGER DEFAULT 0;

-- 2. Actualización de Sedes
ALTER TABLE sedes ADD COLUMN stock_minimo_referencia INTEGER DEFAULT 25;
-- Actualizar Florencia como Sede Administrativa con mayor umbral
UPDATE sedes SET stock_minimo_referencia = 200 WHERE tipo = 'PRINCIPAL';

-- 3. Actualización de Entregas
ALTER TABLE entregas ADD COLUMN numero_orden TEXT;

-- 4. Actualización de Pacientes
ALTER TABLE pacientes ADD COLUMN sede_id INTEGER REFERENCES sedes(id);
ALTER TABLE pacientes ADD COLUMN fecha_nacimiento DATE;
ALTER TABLE pacientes ADD COLUMN genero TEXT;

-- 5. Creación de Tabla de Copagos
CREATE TABLE IF NOT EXISTS copagos (
    id SERIAL PRIMARY KEY,
    paciente_id TEXT REFERENCES pacientes(documento),
    monto DECIMAL(10,2),
    estado TEXT CHECK(estado IN ('PENDIENTE', 'PAGADO')) DEFAULT 'PENDIENTE',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
