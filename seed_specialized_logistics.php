<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    echo "🏭 CARGANDO PROVEEDORES Y PRODUCTOS ESPECIALIZADOS...\n";

    // 1. Ampliar Categorías
    $nuevasCats = ['ODONTOLOGÍA', 'PLANIFICACIÓN FAMILIAR', 'PRUEBAS DIAGNÓSTICAS', 'GASES MEDICINALES', 'MATERIAL QUIRÚRGICO'];
    $stmtCat = $db->prepare("INSERT INTO categorias (nombre) VALUES (?) ON CONFLICT (nombre) DO NOTHING RETURNING id");
    $catMap = [];
    foreach ($nuevasCats as $c) {
        $stmtCat->execute([$c]);
        $id = $stmtCat->fetchColumn();
        $catMap[$c] = $id ?: $db->query("SELECT id FROM categorias WHERE nombre = '$c'")->fetchColumn();
    }

    // 2. Insertar 10 Proveedores Jurídicos
    $proveedores = [
        ['900123456-1', 'DENTAL COLOMBIA S.A.S', 'Carlos Mario Restrepo', 'Especialistas en insumos odontológicos de alta calidad.'],
        ['860456789-2', 'FARMA-PLANNING GLOBAL', 'Elena Velásquez', 'Distribuidor líder en anticonceptivos y salud sexual.'],
        ['901987654-3', 'LABORATORIOS DIAGNÓSTIX', 'Samuel Torres', 'Pruebas de ETS, Citología y diagnósticos rápidos.'],
        ['800321654-4', 'OXÍGENOS DEL CAQUETÁ', 'Hernán Darío Gómez', 'Suministro de gases medicinales y mantenimiento de balas.'],
        ['900753159-5', 'BIOMÉDICOS JERINGAS Y MÁS', 'Martha Lucía Ortiz', 'Dispositivos médicos descartables y esterilizados.'],
        ['890159487-6', 'DISTRI-AMPOLLAS NACIONAL', 'Ricardo Quintero', 'Especialistas en medicamentos inyectables y ampollas.'],
        ['901357246-7', 'SALUD REPRODUCTIVA E.U.', 'Patricia Caicedo', 'Apoyo integral a programas de planificación familiar.'],
        ['800246813-8', 'QUIRÚRGICOS DEL VALLE', 'Andrés Felipe Arias', 'Todo en material quirúrgico y kits de citología.'],
        ['900654321-9', 'INSU-DENTAL REGIONAL', 'Jorge Eliécer Gaitán Jr.', 'Insumos odontológicos para sedes rurales.'],
        ['830123987-0', 'BIO-TEST SOLUTIONS', 'Sofía Lorenza', 'Reactivos y pruebas diagnósticas especializadas.']
    ];

    $db->exec("ALTER TABLE proveedores ADD COLUMN IF NOT EXISTS representante_legal TEXT;");
    $db->exec("ALTER TABLE proveedores ADD COLUMN IF NOT EXISTS descripcion TEXT;");

    $stmtProv = $db->prepare("INSERT INTO proveedores (nit, razon_social, representante_legal, descripcion) VALUES (?, ?, ?, ?) ON CONFLICT (nit) DO UPDATE SET razon_social = EXCLUDED.razon_social");
    foreach($proveedores as $p) $stmtProv->execute($p);

    // 3. Insertar Productos Especializados
    $productos = [
        ['Kit Resinador Odontológico', 'ODONTOLOGÍA', 'Kit x 5 jeringas', '3M'],
        ['Amalgama Dental Plata', 'ODONTOLOGÍA', 'Frasco x 50 cápsulas', 'COLTENE'],
        ['Preservativos de Látex', 'PLANIFICACIÓN FAMILIAR', 'Caja x 144', 'TODAY'],
        ['Prueba Rápida VIH/Sífilis', 'PRUEBAS DIAGNÓSTICAS', 'Caja x 20 test', 'ABBOTT'],
        ['Cilindro Oxígeno Medicinal (Bala)', 'GASES MEDICINALES', 'Unidad 1m3', 'LINDE'],
        ['Ampolla Adrenalina 1mg/ml', 'GASES MEDICINALES', 'Caja x 10', 'GENFAR'],
        ['Kit Citología Estéril', 'PRUEBAS DIAGNÓSTICAS', 'Unidad con espátula/cepillo', 'LAPROFF'],
        ['Jeringa de Insulina 100 UI', 'MATERIAL QUIRÚRGICO', 'Caja x 100', 'BD'],
        ['Anticonceptivo Mensual Inyectable', 'PLANIFICACIÓN FAMILIAR', 'Ampolla 1ml', 'NOFERTYL'],
        ['Espéculo Vaginal Descartable', 'PRUEBAS DIAGNÓSTICAS', 'Unidad Talla M', 'TEXPON']
    ];

    $stmtProd = $db->prepare("INSERT INTO productos (nombre_generico, categoria_id, concentracion_presentacion, laboratorio) VALUES (?, ?, ?, ?) ON CONFLICT DO NOTHING");
    foreach($productos as $prod) {
        $stmtProd->execute([$prod[0], $catMap[$prod[1]], $prod[2], $prod[3]]);
    }

    $db->commit();
    echo "✅ ÉXITO: Proveedores y productos especializados cargados.\n";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
