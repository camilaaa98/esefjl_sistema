<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    echo "🛠️ AÑADIENDO RESTRICCIÓN DE UNICIDAD A INVENTARIO...\n";
    
    // 1. Eliminar duplicados si existen (manteniendo el que tenga más stock)
    $db->exec("
        DELETE FROM inventario a USING inventario b
        WHERE a.id < b.id 
        AND a.sede_id = b.sede_id 
        AND a.producto_id = b.producto_id
    ");

    // 2. Añadir la restricción UNIQUE
    $db->exec("ALTER TABLE inventario ADD CONSTRAINT unique_sede_producto UNIQUE (sede_id, producto_id)");
    
    echo "✅ Restricción 'unique_sede_producto' añadida con éxito.\n";

} catch (Exception $e) {
    echo "⚠️ AVISO: " . $e->getMessage() . " (Posiblemente ya existe)\n";
}
