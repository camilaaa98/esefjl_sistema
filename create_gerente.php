<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    
    // 1. Obtener ID de Gerente
    $rol = $db->query("SELECT id FROM roles WHERE nombre = 'Gerente'")->fetchColumn();
    $sede = $db->query("SELECT id FROM sedes WHERE tipo = 'PRINCIPAL'")->fetchColumn();

    if (!$rol || !$sede) {
        die("❌ Error: No se encontró el rol de Gerente o la Sede Principal.\n");
    }

    // 2. Crear o actualizar Gerente
    $pass = password_hash('Ese2026*', PASSWORD_DEFAULT);
    $stmt = $db->prepare("
        INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id) 
        VALUES ('30000000', 'Gerente', 'General', 'g_gerente', ?, ?, ?)
        ON CONFLICT (username) DO UPDATE SET password = EXCLUDED.password, rol_id = EXCLUDED.rol_id, sede_id = EXCLUDED.sede_id
    ");
    $stmt->execute([$pass, $rol, $sede]);

    echo "✅ USUARIO g_gerente CREADO/ACTUALIZADO EXITOSAMENTE.\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
