-- Actualización para Alertas de Inactividad
ALTER TABLE sedes ADD COLUMN IF NOT EXISTS ultima_pedido_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Asegurar Roles Actualizados
INSERT INTO roles (nombre) 
VALUES 
('Gerente'),
('Subgerente de Servicios de Salud'),
('Subgerente Administrativa y Financiera'),
('Regente Farmacia'),
('Salud'),
('Administrativo'),
('Seguridad'),
('SECRETARIA DE SALUD DEPARTAMENTAL'),
('PRESIDENTES JUNTA DIRECTIVA'),
('REPRESENTANTE SECTOR ADMINISTRATIVO'),
('REPRESENTANTE SECTOR SALUD'),
('SECRETARIO TECNICO Y GERENTE DE LA ESE FABIO JARAMILLO LONDOÑO'),
('REPRESENTANTE DE LOS USUARIOS')
ON CONFLICT (nombre) DO NOTHING;
