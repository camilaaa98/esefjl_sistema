<?php
/**
 * Parche Crítico de Base de Datos - FARMACIA ESEFJL
 * Repara el error "no existe tal columna: p.laboratorio"
 */
try {
    $db = new PDO('sqlite:database/esefjl.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== INICIANDO REPARACIí“N DE ESQUEMA ===\n";

    // 1. Reparar tabla 'productos'
    $cols_productos = [
        'nombre_comercial' => 'TEXT',
        'concentracion_presentacion' => 'TEXT',
        'laboratorio' => 'TEXT',
        'descripcion' => 'TEXT',
        'categoria_id' => 'INTEGER',
        'unidad_medida' => 'TEXT'
    ];

    foreach ($cols_productos as $col => $type) {
        try {
            $db->exec("ALTER TABLE productos ADD COLUMN $col $type");
            echo "✅ Columna '$col' añadida a 'productos'.\n";
        } catch (PDOException $e) {
            // Probablemente ya existe
            echo "â„¹ï¸ Columna '$col' ya existe en 'productos' o error menor.\n";
        }
    }

    // 2. Reparar tabla 'inventario' (asegurar campos de vencimiento)
    $cols_inventario = [
        'lote' => 'TEXT',
        'fecha_vencimiento' => 'DATE'
    ];

    foreach ($cols_inventario as $col => $type) {
        try {
            $db->exec("ALTER TABLE inventario ADD COLUMN $col $type");
            echo "✅ Columna '$col' añadida a 'inventario'.\n";
        } catch (PDOException $e) {
            echo "â„¹ï¸ Columna '$col' ya existe en 'inventario'.\n";
        }
    }

    // 3. Reparar tabla 'sedes'
    try {
        $db->exec("ALTER TABLE sedes ADD COLUMN stock_minimo_referencia INTEGER DEFAULT 25");
        $db->exec("UPDATE sedes SET stock_minimo_referencia = 200 WHERE tipo = 'PRINCIPAL'");
        echo "✅ Tabla 'sedes' actualizada.\n";
    } catch (PDOException $e) { echo "â„¹ï¸ 'sedes' ya estaba actualizada.\n"; }

    // 4. Reparar tabla 'pacientes'
    try {
        $db->exec("ALTER TABLE pacientes ADD COLUMN sede_id INTEGER");
        $db->exec("ALTER TABLE pacientes ADD COLUMN fecha_nacimiento DATE");
        $db->exec("ALTER TABLE pacientes ADD COLUMN genero TEXT");
        echo "✅ Tabla 'pacientes' actualizada.\n";
    } catch (PDOException $e) { echo "â„¹ï¸ 'pacientes' ya estaba actualizada.\n"; }

    // 5. Reparar tabla 'entregas'
    try {
        $db->exec("ALTER TABLE entregas ADD COLUMN numero_orden TEXT");
        echo "✅ Tabla 'entregas' actualizada.\n";
    } catch (PDOException $e) { echo "â„¹ï¸ 'entregas' ya estaba actualizada.\n"; }

    // 6. Crear tabla 'copagos'
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS copagos (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            paciente_id TEXT, 
            monto REAL, 
            estado TEXT DEFAULT 'PENDIENTE', 
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "✅ Tabla 'copagos' creada.\n";
    } catch (PDOException $e) { echo "â„¹ï¸ 'copagos' ya existía.\n"; }

    // 7. Nuevos flags en 'productos'
    try {
        $db->exec("ALTER TABLE productos ADD COLUMN requiere_frio INTEGER DEFAULT 0");
        $db->exec("ALTER TABLE productos ADD COLUMN es_delicado INTEGER DEFAULT 0");
        echo "✅ Tabla 'productos' actualizada con flags.\n";
    } catch (PDOException $e) { echo "â„¹ï¸ 'productos' ya tenía flags.\n"; }

    echo "=== REPARACIí“N COMPLETADA CON í‰XITO ===\n";

} catch (Exception $e) {
    echo "âŒ ERROR FATAL: " . $e->getMessage() . "\n";
}
?>
