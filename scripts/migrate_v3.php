<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();

try {
    $db->exec("ALTER TABLE productos ADD COLUMN imagen_url TEXT");
} catch (Exception $e) {}

try {
    $db->exec("ALTER TABLE productos ADD COLUMN valor_unitario REAL DEFAULT 0");
} catch (Exception $e) {}

try {
    $db->exec("ALTER TABLE productos ADD COLUMN descripcion_breve TEXT");
} catch (Exception $e) {}

// Poblado inicial de datos para demostración
$db->exec("UPDATE productos SET valor_unitario = 4500, descripcion_breve = 'Analgésico y antipirético de alta pureza.' WHERE nombre_generico LIKE '%Acetaminofén%'");
$db->exec("UPDATE productos SET valor_unitario = 12800, descripcion_breve = 'Antiviral potente para tratamiento cutáneo.' WHERE nombre_generico LIKE '%Aciclovir%'");
$db->exec("UPDATE productos SET valor_unitario = 8500, descripcion_breve = 'Antibiótico de amplio espectro para infecciones.' WHERE nombre_generico LIKE '%Amoxicilina%'");

echo "Migration successful: Added imagen_url, valor_unitario, and descripcion_breve.";
?>
