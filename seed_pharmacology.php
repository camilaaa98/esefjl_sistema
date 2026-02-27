<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    echo "💊 CARGANDO CATÁLOGO FARMACÉUTICO PROFESIONAL...\n";

    // 1. Limpiar inventario y productos previos
    $db->exec("DELETE FROM inventario; DELETE FROM productos;");

    // 2. Definición profesional de medicamentos
    $productos = [
        // Analgésicos y AINES (Diferentes dosis y laboratorios)
        ['Ibuprofeno 400mg Tabletas', 'Analgésico', 'Caja x 20', 'MK'],
        ['Ibuprofeno 400mg Tabletas', 'Analgésico', 'Caja x 30', 'GENFAR'],
        ['Ibuprofeno 600mg Tabletas', 'Analgésico', 'Frasco x 50', 'BAYER'],
        ['Acetaminofén 500mg Tabletas', 'Analgésico', 'Caja x 100', 'LAPROFF'],
        ['Acetaminofén 150mg/5ml Jarabe', 'Analgésico Pediátrico', 'Frasco 120ml', 'MK'],
        ['Diclofenaco 75mg/3ml Ampolla', 'Antiinflamatorio', 'Unidad (Inyectable)', 'GENFAR'],
        
        // Antibióticos
        ['Amoxicilina 500mg Cápsulas', 'Antibiótico', 'Caja x 30', 'MK'],
        ['Amoxicilina 500mg Cápsulas', 'Antibiótico', 'Caja x 15', 'ABBOTT'],
        ['Ciprofloxacina 500mg Tableta', 'Antibiótico', 'Caja x 10', 'LAFRANCOL'],
        
        // Antihipertensivos
        ['Losartán 50mg Tabletas', 'Cardiovascular', 'Caja x 30', 'HUMAX'],
        ['Enalapril 20mg Tabletas', 'Cardiovascular', 'Caja x 20', 'GENFAR'],
        
        // Insumos Quirúrgicos
        ['Jeringa 5cc con aguja 21G', 'Dispositivo Médico', 'Unidad estéril', 'BD'],
        ['Gasa estéril 7.5x7.5cm', 'Dispositivo Médico', 'Paquete x 2', 'TEXPON'],
        ['Guantes de látex Talle M', 'Protección', 'Caja x 100', 'TOP GLOVE']
    ];

    $stmtProd = $db->prepare("INSERT INTO productos (nombre_generico, categoria, concentracion_presentacion, laboratorio) VALUES (?, ?, ?, ?)");
    
    $productIds = [];
    foreach ($productos as $p) {
        $stmtProd->execute($p);
        $productIds[] = $db->lastInsertId();
    }

    // 3. Cargar inventario diferenciado por sede
    $sedes = $db->query("SELECT id, nombre, tipo FROM sedes")->fetchAll();
    $stmtInv = $db->prepare("INSERT INTO inventario (sede_id, producto_id, stock_actual, stock_minimo, fecha_vencimiento, lote) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($sedes as $sede) {
        echo "   -> Distribuyendo en sede: {$sede['nombre']}...\n";
        foreach ($productIds as $pId) {
            // Lógica de stock: La principal tiene mucho, los municipios poco
            $stockBase = ($sede['tipo'] === 'PRINCIPAL') ? rand(500, 2000) : rand(10, 100);
            $stockMin = ($sede['tipo'] === 'PRINCIPAL') ? 100 : rand(5, 20);
            
            // Fecha de vencimiento futura aleatoria
            $venc = date('Y-m-d', strtotime('+' . rand(6, 36) . ' months'));
            $lote = "L-" . strtoupper(substr(md5(rand()), 0, 5));

            $stmtInv->execute([$sede['id'], $pId, $stockBase, $stockMin, $venc, $lote]);
        }
    }

    $db->commit();
    echo "✅ ÉXITO: 14 productos cargados con stock diferenciado en todas las sedes.\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
