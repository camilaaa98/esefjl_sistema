<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    echo "📦 INICIANDO INYECCIÓN MASIVA DE STOCK (100,000 UND POR PRODUCTO CRÍTICO)...\n";

    // 1. Obtener IDs de los productos especializados inyectados recientemente
    $productos_nombres = [
        'Kit Resinador Odontológico', 'Amalgama Dental Plata', 'Preservativos de Látex',
        'Prueba Rápida VIH/Sífilis', 'Cilindro Oxígeno Medicinal (Bala)', 
        'Ampolla Adrenalina 1mg/ml', 'Kit Citología Estéril', 'Jeringa de Insulina 100 UI',
        'Anticonceptivo Mensual Inyectable', 'Espéculo Vaginal Descartable'
    ];

    $placeholders = implode(',', array_fill(0, count($productos_nombres), '?'));
    $stmtProd = $db->prepare("SELECT id FROM productos WHERE nombre_generico IN ($placeholders)");
    $stmtProd->execute($productos_nombres);
    $prodIds = $stmtProd->fetchAll(PDO::FETCH_COLUMN);

    if (empty($prodIds)) {
        throw new Exception("No se encontraron los productos especializados en la base de datos.");
    }

    // 2. Obtener el ID de la sede principal (Florencia/CEDIS)
    $florencia_id = $db->query("SELECT id FROM sedes WHERE nombre LIKE '%Florencia%' LIMIT 1")->fetchColumn();
    
    if (!$florencia_id) {
        throw new Exception("No se encontró la sede de Florencia.");
    }

    // 3. Actualizar o Insertar 100,000 unidades en el inventario de Florencia
    $stmtInv = $db->prepare("
        INSERT INTO inventario (sede_id, producto_id, stock_actual, stock_minimo, fecha_vencimiento, lote)
        VALUES (?, ?, 100000, 5000, '2028-12-31', 'LT-MAX-001')
        ON CONFLICT (sede_id, producto_id) DO UPDATE 
        SET stock_actual = inventario.stock_actual + 100000, stock_minimo = 5000
    ");

    foreach ($prodIds as $id) {
        $stmtInv->execute([$florencia_id, $id]);
    }

    $db->commit();
    echo "✅ ÉXITO: Se han inyectado 1,000,000 de unidades totales (100k por ítem) en el Almacén Central.\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
