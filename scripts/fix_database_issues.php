<?php
/**
 * Script para corregir problemas de la base de datos
 */

require_once __DIR__ . '/../app/config/Database.php';

echo "=== CORRIGIENDO PROBLEMAS DE BASE DE DATOS ===\n\n";

$db = Database::getInstance();

// 1. Agregar columna regimen a pacientes si no existe
try {
    $columns = $db->query("PRAGMA table_info(pacientes)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('regimen', $columns)) {
        $db->exec("ALTER TABLE pacientes ADD COLUMN regimen TEXT DEFAULT 'SUBSIDIADO'");
        echo "✅ Columna 'regimen' agregada a pacientes\n";
    } else {
        echo "✅ Columna 'regimen' ya existe\n";
    }
} catch (Exception $e) {
    echo "âš ï¸  Error con columna regimen: " . $e->getMessage() . "\n";
}

// 2. Actualizar CHECK constraint de pedidos_municipios para permitir DESPACHADO
try {
    // SQLite no permite modificar constraints directamente, necesitamos recrear la tabla
    $db->exec("
        CREATE TABLE pedidos_municipios_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sede_solicitante_id INTEGER NOT NULL,
            fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            estado TEXT CHECK(estado IN ('PENDIENTE', 'EN_CAMINO', 'ENTREGADO', 'DESPACHADO', 'RECHAZADO')) DEFAULT 'PENDIENTE',
            observaciones TEXT
        )
    ");
    
    // Copiar datos existentes
    $db->exec("
        INSERT INTO pedidos_municipios_new (id, sede_solicitante_id, fecha_solicitud, estado, observaciones)
        SELECT id, sede_solicitante_id, fecha_solicitud, 
               CASE WHEN estado = 'DESPACHADO' THEN 'ENTREGADO' ELSE estado END,
               NULL
        FROM pedidos_municipios
    ");
    
    // Eliminar tabla vieja y renombrar
    $db->exec("DROP TABLE pedidos_municipios");
    $db->exec("ALTER TABLE pedidos_municipios_new RENAME TO pedidos_municipios");
    
    echo "✅ Tabla pedidos_municipios actualizada con nuevos estados\n";
} catch (Exception $e) {
    echo "âš ï¸  Error actualizando pedidos_municipios: " . $e->getMessage() . "\n";
}

// 3. Verificar que el RequestController funcione con los nuevos estados
echo "\n=== VERIFICACIí“N ===\n";
$estados = $db->query("SELECT DISTINCT estado FROM pedidos_municipios")->fetchAll(PDO::FETCH_COLUMN);
echo "Estados actuales en pedidos: " . implode(', ', $estados) . "\n";

echo "\n✅ Correcciones aplicadas!\n";
?>
