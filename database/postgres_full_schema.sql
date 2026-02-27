-- Esquema SQL Completo para PostgreSQL - SISFARMA PRO
-- ESE Fabio Jaramillo Londoño

-- 1. Municipios / Sedes
CREATE TABLE IF NOT EXISTS sedes (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    direccion TEXT NOT NULL,
    jefe_encargado TEXT,
    celular_jefe TEXT,
    tipo TEXT CHECK(tipo IN ('PRINCIPAL', 'MUNICIPIO')) DEFAULT 'MUNICIPIO'
);

-- 2. Usuarios del Sistema
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    documento TEXT NOT NULL UNIQUE,
    nombres TEXT NOT NULL,
    apellidos TEXT NOT NULL,
    correo TEXT,
    celular TEXT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    rol_id INTEGER REFERENCES roles(id),
    sede_id INTEGER REFERENCES sedes(id),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Pacientes (Con campos de Copago / Ley 1448)
CREATE TABLE IF NOT EXISTS pacientes (
    documento TEXT PRIMARY KEY,
    nombres TEXT NOT NULL,
    apellidos TEXT NOT NULL,
    correo TEXT,
    celular TEXT,
    direccion TEXT,
    barrio TEXT,
    eps TEXT,
    regimen TEXT CHECK(regimen IN ('CONTRIBUTIVO', 'SUBSIDIADO')),
    es_desplazado BOOLEAN DEFAULT FALSE,
    sisben TEXT
);

-- 4. Categorización y Maestro de Productos
CREATE TABLE IF NOT EXISTS categorias (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    codigo_invima TEXT UNIQUE,
    nombre_generico TEXT NOT NULL,
    nombre_comercial TEXT,
    descripcion TEXT,
    categoria_id INTEGER REFERENCES categorias(id),
    unidad_medida TEXT
);

-- 5. Inventario
CREATE TABLE IF NOT EXISTS inventario (
    id SERIAL PRIMARY KEY,
    sede_id INTEGER REFERENCES sedes(id),
    producto_id INTEGER REFERENCES productos(id),
    stock_actual INTEGER DEFAULT 0,
    stock_minimo INTEGER DEFAULT 0,
    lote TEXT,
    fecha_vencimiento DATE
);

-- 6. Proveedores y Compras
CREATE TABLE IF NOT EXISTS proveedores (
    id SERIAL PRIMARY KEY,
    nit TEXT UNIQUE NOT NULL,
    razon_social TEXT NOT NULL,
    contacto TEXT,
    telefono TEXT,
    email TEXT
);

CREATE TABLE IF NOT EXISTS compras (
    id SERIAL PRIMARY KEY,
    proveedor_id INTEGER REFERENCES proveedores(id),
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(12,2),
    estado_pago TEXT CHECK(estado_pago IN ('PENDIENTE', 'PAGADO')) DEFAULT 'PENDIENTE'
);

-- 7. Solicitudes y Entregas
CREATE TABLE IF NOT EXISTS pedidos_municipios (
    id SERIAL PRIMARY KEY,
    sede_solicitante_id INTEGER REFERENCES sedes(id),
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado TEXT CHECK(estado IN ('PENDIENTE', 'EN_CAMINO', 'ENTREGADO')) DEFAULT 'PENDIENTE'
);

CREATE TABLE IF NOT EXISTS entregas (
    id SERIAL PRIMARY KEY,
    paciente_id TEXT REFERENCES pacientes(documento),
    producto_id INTEGER REFERENCES productos(id),
    cantidad INTEGER,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega TIMESTAMP,
    estado TEXT CHECK(estado IN ('SOLICITADO', 'ENTREGADO')) DEFAULT 'SOLICITADO',
    sede_id INTEGER REFERENCES sedes(id)
);

-- Semilla (Datos Iniciales)
INSERT INTO roles (nombre) 
VALUES ('Administrador'), ('Regente Farmacia'), ('Salud'), ('Administrativo'), ('Seguridad')
ON CONFLICT (nombre) DO NOTHING;

INSERT INTO sedes (nombre, direccion, tipo) 
VALUES 
('Florencia (Principal)', 'Sede Central', 'PRINCIPAL'),
('Solita', 'Calle Principal', 'MUNICIPIO'),
('Solano', 'Calle Principal', 'MUNICIPIO'),
('Milán', 'Calle 3 No. 6-72', 'MUNICIPIO'),
('San Antonio de Getucha', 'Calle Principal', 'MUNICIPIO'),
('Valparaíso', 'Calle 10 Carrera 3', 'MUNICIPIO')
ON CONFLICT DO NOTHING;
