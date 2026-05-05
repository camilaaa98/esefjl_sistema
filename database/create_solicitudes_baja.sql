-- Tabla para solicitudes de baja de medicamentos vencidos
CREATE TABLE IF NOT EXISTS solicitudes_baja (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    inventario_id INTEGER NOT NULL,
    producto_id INTEGER NOT NULL,
    lote TEXT NOT NULL,
    cantidad INTEGER NOT NULL,
    motivo TEXT,
    solicitante_id INTEGER NOT NULL,
    sede_id INTEGER NOT NULL,
    estado TEXT DEFAULT 'pendiente',
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_aprobacion DATETIME,
    aprobado_por INTEGER,
    FOREIGN KEY (inventario_id) REFERENCES inventario(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id)
);
