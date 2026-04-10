<?php
require_once __DIR__ . '/core/Database.php';
try {
    $db = Database::getInstance();
    $user = $db->query("SELECT u.username, r.nombre as rol FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.username = 'g_gerente'")->fetch();
    if($user) {
        echo "✅ USUARIO ENCONTRADO: {$user['username']} con rol de {$user['rol']}\n";
    } else {
        echo "❌ USUARIO NO ENCONTRADO: El gerente no está en la base de datos.\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
