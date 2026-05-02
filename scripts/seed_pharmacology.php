<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    echo "âš™ï¸ ACTUALIZANDO ESQUEMA DE PRODUCTOS...\n";
    $db->exec("ALTER TABLE productos ADD COLUMN IF NOT EXISTS concentracion_presentacion TEXT;");
    $db->exec("ALTER TABLE productos ADD COLUMN IF NOT EXISTS laboratorio TEXT;");

    echo "ðŸ’Š CARGANDO CATíLOGO FARMACí‰UTICO PROFESIONAL...\n";

    // 1. Limpiar inventario y productos previos
    $db->exec("DELETE FROM inventario; DELETE FROM entregas; DELETE FROM productos; DELETE FROM categorias;");

    // 2. Insertar Categorías Profesionales
    $cats = ['ANALGí‰SICOS Y AINES', 'ANTIBIí“TICOS', 'CARDIOVASCULAR', 'DISPOSITIVOS Mí‰DICOS', 'PROTECCIí“N'];
    $stmtCat = $db->prepare("INSERT INTO categorias (nombre) VALUES (?) ON CONFLICT DO NOTHING RETURNING id");
    $catMap = [];
    foreach ($cats as $c) {
        $stmtCat->execute([$c]);
        $catMap[$c] = $stmtCat->fetchColumn();
        if (!$catMap[$c]) {
            // Fallback si no hay RETURNING (algunos drivers)
            $catMap[$c] = $db->query("SELECT id FROM categorias WHERE nombre = '$c'")->fetchColumn();
        }
    }

    // 3. Definición profesional de medicamentos
    $productos = [
        ['Ibuprofeno 400mg Tabletas', 'ANALGí‰SICOS Y AINES', 'Caja x 20', 'MK'],
        ['Ibuprofeno 400mg Tabletas', 'ANALGí‰SICOS Y AINES', 'Caja x 30', 'GENFAR'],
        ['Ibuprofeno 600mg Tabletas', 'ANALGí‰SICOS Y AINES', 'Frasco x 50', 'BAYER'],
        ['Acetaminofén 500mg Tabletas', 'ANALGí‰SICOS Y AINES', 'Caja x 100', 'LAPROFF'],
        ['Amoxicilina 500mg Cápsulas', 'ANTIBIí“TICOS', 'Caja x 30', 'MK'],
        ['Losartán 50mg Tabletas', 'CARDIOVASCULAR', 'Caja x 30', 'HUMAX'],
        ['Jeringa 5cc con aguja 21G', 'DISPOSITIVOS Mí‰DICOS', 'Unidad estéril', 'BD'],
        ['Guantes de látex Talle M', 'PROTECCIí“N', 'Caja x 100', 'TOP GLOVE']
    ];

    $stmtProd = $db->prepare("INSERT INTO productos (nombre_generico, categoria_id, concentracion_presentacion, laboratorio) VALUES (?, ?, ?, ?)");
    
    $productIds = [];
    foreach ($productos as $p) {
        $stmtProd->execute([$p[0], $catMap[$p[1]], $p[2], $p[3]]);
        $productIds[] = $db->lastInsertId();
    }

    // 4. Cargar inventario diferenciado por sede
    $sedes = $db->query("SELECT id, nombre, tipo FROM sedes")->fetchAll();
    $stmtInv = $db->prepare("INSERT INTO inventario (sede_id, producto_id, stock_actual, stock_minimo, fecha_vencimiento, lote) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($sedes as $sede) {
        echo "   -> Distribuyendo en sede: {$sede['nombre']}...\n";
        foreach ($productIds as $pId) {
            // Lógica de stock: La principal tiene mucho, los municipios poco
            $stockBase = ($sede['tipo'] === 'PRINCIPAL') ? rand(500, 2000) : rand(0, 50);
            $stockMin = ($sede['tipo'] === 'PRINCIPAL') ? 100 : rand(10, 20);
            
            $venc = date('Y-m-d', strtotime('+' . rand(6, 36) . ' months'));
            $lote = "L-" . strtoupper(substr(md5(rand()), 0, 5));

            $stmtInv->execute([$sede['id'], $pId, $stockBase, $stockMin, $venc, $lote]);
        }
    }

    $db->commit();
    echo "✅ í‰XITO: Catálogo profesional cargado y distribuido.\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "âŒ ERROR: " . $e->getMessage() . "\n";
}
