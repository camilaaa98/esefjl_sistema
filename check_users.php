<?php
require_once __DIR__ . '/core/Database.php';
try {
    $db = Database::getInstance();
    $users = $db->query("SELECT u.username, r.nombre as rol FROM usuarios u JOIN roles r ON u.rol_id = r.id LIMIT 20")->fetchAll();
    echo "🔍 Verificando Usuarios en DB:\n";
    foreach($users as $u) {
        echo "- {$u['username']} ({$u['rol']})\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
