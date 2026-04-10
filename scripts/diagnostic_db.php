<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    echo "🔍 Verificando Esquema...\n";

    // 1. Crear tabla de detalles si no existe
    $db->exec("
        CREATE TABLE IF NOT EXISTS detalles_pedido_municipio (
            id SERIAL PRIMARY KEY,
            pedido_id INTEGER REFERENCES pedidos_municipios(id),
            producto_id INTEGER REFERENCES productos(id),
            cantidad INTEGER,
            estado TEXT DEFAULT 'PENDIENTE'
        );
    ");
    echo "✅ Tabla detalles_pedido_municipio lista.\n";

    // 2. Limpiar transacciones colgadas (opcional, depende del driver)
    // En Postgres, si una sesión falla, suele hacer rollback. 
    // Pero verificamos si hay tablas bloqueadas si fuera necesario.

    // 3. Probar una consulta de inventario para medir velocidad
    $t1 = microtime(true);
    $db->query("SELECT COUNT(*) FROM inventario")->fetch();
    $t2 = microtime(true);
    echo "⚡ Velocidad DB: " . round(($t2 - $t1) * 1000, 2) . "ms\n";

    echo "✅ Todo operativo.";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
