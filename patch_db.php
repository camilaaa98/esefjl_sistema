<?php
/**
 * Parche Crítico de Base de Datos - SISFARMA PRO
 * Repara el error "no existe tal columna: p.laboratorio"
 */
try {
    $db = new PDO('sqlite:core/esefjl.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== INICIANDO REPARACIÓN DE ESQUEMA ===\n";

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
            echo "ℹ️ Columna '$col' ya existe en 'productos' o error menor.\n";
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
            echo "ℹ️ Columna '$col' ya existe en 'inventario'.\n";
        }
    }

    // 3. Reparar tabla 'sedes' (campo de actividad)
    try {
        $db->exec("ALTER TABLE sedes ADD COLUMN ultima_pedido_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "✅ Columna 'ultima_pedido_at' añadida a 'sedes'.\n";
    } catch (PDOException $e) {
        echo "ℹ️ Columna 'ultima_pedido_at' ya existe en 'sedes'.\n";
    }

    echo "=== REPARACIÓN COMPLETADA CON ÉXITO ===\n";

} catch (Exception $e) {
    echo "❌ ERROR FATAL: " . $e->getMessage() . "\n";
}
?>
