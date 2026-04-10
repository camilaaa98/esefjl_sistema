<?php
require_once __DIR__ . '/core/Database.php';
try {
    $db = Database::getInstance();
    $pac = $db->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
    $usr = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $roles = $db->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    echo "📊 Conteo Actual:\n";
    echo "- Pacientes: $pac\n";
    echo "- Usuarios: $usr\n";
    echo "- Roles: $roles (Debería ser 13+)\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
