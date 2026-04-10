<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    
    // 1. Obtener IDs de roles y sedes
    $stmtSede = $db->query("SELECT id FROM sedes WHERE nombre LIKE 'Florencia%' LIMIT 1");
    $sedeAdmin = $stmtSede->fetchColumn();
    
    $stmtSedeSolita = $db->query("SELECT id FROM sedes WHERE nombre = 'Solita' LIMIT 1");
    $sedeSolita = $stmtSedeSolita->fetchColumn();
    
    $stmtRolAdmin = $db->query("SELECT id FROM roles WHERE nombre = 'Administrador' LIMIT 1");
    $rolAdmin = $stmtRolAdmin->fetchColumn();
    
    $stmtRolRegente = $db->query("SELECT id FROM roles WHERE nombre = 'Regente Farmacia' LIMIT 1");
    $rolRegente = $stmtRolRegente->fetchColumn();

    // 2. Insertar Admin
    $passAdmin = password_hash('Admin2026*', PASSWORD_DEFAULT);
    $db->prepare("INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id) 
                  VALUES ('123456', 'Administrador', 'General', 'admin', ?, ?, ?)
                  ON CONFLICT (username) DO UPDATE SET password = EXCLUDED.password")
       ->execute([$passAdmin, $rolAdmin, $sedeAdmin]);

    // 3. Insertar Jefe de IPS Solita
    $passSolita = password_hash('Solita2026*', PASSWORD_DEFAULT);
    $db->prepare("INSERT INTO usuarios (documento, nombres, apellidos, username, password, rol_id, sede_id) 
                  VALUES ('789012', 'Jefe IPS', 'Solita', 'jefe_solita', ?, ?, ?)
                  ON CONFLICT (username) DO UPDATE SET password = EXCLUDED.password")
       ->execute([$passSolita, $rolRegente, $sedeSolita]);

    echo "✅ ÉXITO: Usuarios iniciales creados exitosamente.\n";
    echo "🔑 Admin: admin / Admin2026*\n";
    echo "🔑 IPS: jefe_solita / Solita2026*\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
?>
