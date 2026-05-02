<?php
try {
    $db = new PDO('sqlite:database/esefjl.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Helper function to check if column exists
    function columnExists($db, $table, $column) {
        $schema = $db->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($schema as $col) {
            if ($col['name'] === $column) return true;
        }
        return false;
    }

    // Update sedes
    if (!columnExists($db, 'sedes', 'stock_minimo_referencia')) {
        $db->exec("ALTER TABLE sedes ADD COLUMN stock_minimo_referencia INTEGER DEFAULT 25");
        $db->exec("UPDATE sedes SET stock_minimo_referencia = 200 WHERE nombre LIKE '%Florencia%' OR nombre LIKE '%Administrativa%'");
    }
    if (!columnExists($db, 'sedes', 'tiene_nevera')) {
        $db->exec("ALTER TABLE sedes ADD COLUMN tiene_nevera BOOLEAN DEFAULT FALSE");
    }

    // Update productos
    if (!columnExists($db, 'productos', 'requiere_frio')) {
        $db->exec("ALTER TABLE productos ADD COLUMN requiere_frio BOOLEAN DEFAULT FALSE");
    }
    if (!columnExists($db, 'productos', 'es_delicado')) {
        $db->exec("ALTER TABLE productos ADD COLUMN es_delicado BOOLEAN DEFAULT FALSE");
    }

    // Update pacientes
    if (!columnExists($db, 'pacientes', 'sede_adscripcion_id')) {
        $db->exec("ALTER TABLE pacientes ADD COLUMN sede_adscripcion_id INTEGER");
    }

    // Create copagos table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS copagos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        paciente_id INTEGER,
        monto DECIMAL(10,2),
        estado TEXT, -- 'COBRADO', 'PENDIENTE', 'EXENTO'
        motivo_exencion TEXT,
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create ordenes_medicas table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS ordenes_medicas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        paciente_id INTEGER,
        numero_orden TEXT UNIQUE,
        medico TEXT,
        fecha_emision DATE,
        vigencia_meses INTEGER DEFAULT 1,
        estado TEXT DEFAULT 'ACTIVA'
    )");

    echo "Migration completed successfully.";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
