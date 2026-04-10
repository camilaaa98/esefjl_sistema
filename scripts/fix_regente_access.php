<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    
    // Obtener IDs
    $rolId = $db->query("SELECT id FROM roles WHERE nombre = 'Regente Farmacia'")->fetchColumn();
    $sedeId = $db->query("SELECT id FROM sedes WHERE nombre = 'Florencia (Principal)'")->fetchColumn();
    
    if (!$rolId || !$sedeId) {
        throw new Exception("Rol de Regente o Sede Florencia no encontrados.");
    }

    $pass = password_hash('Ese2026*', PASSWORD_DEFAULT);
    
    // Intentar actualizar si existe o insertar si no
    $stmt = $db->prepare("
        INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id)
        VALUES ('300000', 'Regente', 'Florencia', 'regente_flo', ?, ?, ?)
        ON CONFLICT (username) DO UPDATE SET 
            password = EXCLUDED.password,
            rol_id = EXCLUDED.rol_id,
            sede_id = EXCLUDED.sede_id
    ");
    
    $stmt->execute([$pass, $rolId, $sedeId]);
    
    echo "✅ Acceso Regente Reparado: regente_flo / Ese2026*\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
