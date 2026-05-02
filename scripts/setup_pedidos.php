<?php
/**
 * Script para configurar el sistema de pedidos municipales
 */

require_once __DIR__ . '/../app/config/Database.php';

echo "=== CONFIGURANDO SISTEMA DE PEDIDOS ===\n\n";

$db = Database::getInstance();

// 1. Crear tabla de detalles de pedido si no existe
try {
    $db->exec("CREATE TABLE IF NOT EXISTS detalles_pedido_municipio (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        pedido_id INTEGER NOT NULL,
        producto_id INTEGER NOT NULL,
        cantidad INTEGER NOT NULL,
        FOREIGN KEY (pedido_id) REFERENCES pedidos_municipios(id) ON DELETE CASCADE
    )");
    echo "✅ Tabla detalles_pedido_municipio creada/verificada\n";
} catch (Exception $e) {
    echo "âŒ Error creando tabla detalles: " . $e->getMessage() . "\n";
}

// 2. Verificar columnas en pedidos_municipios
$columns = $db->query("PRAGMA table_info(pedidos_municipios)")->fetchAll(PDO::FETCH_COLUMN, 1);
if (!in_array('estado', $columns)) {
    try {
        $db->exec("ALTER TABLE pedidos_municipios ADD COLUMN estado TEXT DEFAULT 'PENDIENTE'");
        echo "✅ Columna 'estado' agregada a pedidos_municipios\n";
    } catch (Exception $e) {
        echo "âš ï¸  Error agregando columna estado: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ Columna 'estado' ya existe\n";
}

// 3. Mostrar resumen de inventario
$inventario = $db->query("SELECT COUNT(*) FROM inventario")->fetchColumn();
$sedes = $db->query("SELECT COUNT(*) FROM sedes")->fetchColumn();
$productos = $db->query("SELECT COUNT(*) FROM productos")->fetchColumn();

echo "\n=== RESUMEN DEL SISTEMA ===\n";
echo "â€¢ Sedes registradas: {$sedes}\n";
echo "â€¢ Productos en catálogo: {$productos}\n";
echo "â€¢ Items en inventario: {$inventario}\n";

// 4. Mostrar stock por sede
echo "\n=== STOCK POR SEDE ===\n";
$stockPorSede = $db->query("SELECT s.nombre, SUM(i.stock_actual) as total FROM sedes s LEFT JOIN inventario i ON s.id = i.sede_id GROUP BY s.id ORDER BY total DESC")->fetchAll();
foreach ($stockPorSede as $row) {
    $total = $row['total'] ?? 0;
    echo "â€¢ {$row['nombre']}: {$total} unidades\n";
}

echo "\n✅ Configuración completada!\n";
