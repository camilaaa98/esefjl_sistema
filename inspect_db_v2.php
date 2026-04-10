<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    
    echo "=== USUARIOS ===\n";
    $users = $db->query("SELECT id, username, nombres, rol_id, sede_id FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    foreach($users as $u) {
        $rol = $db->query("SELECT nombre FROM roles WHERE id = {$u['rol_id']}")->fetchColumn();
        $sede = $db->query("SELECT nombre FROM sedes WHERE id = {$u['sede_id']}")->fetchColumn();
        echo "ID: {$u['id']} | User: {$u['username']} | Rol: {$rol} | Sede: {$sede}\n";
    }

    echo "\n=== SEDES ===\n";
    $sedes = $db->query("SELECT id, nombre, ultima_pedido_at FROM sedes")->fetchAll(PDO::FETCH_ASSOC);
    foreach($sedes as $s) {
        echo "ID: {$s['id']} | Sede: {$s['nombre']} | Ultima: {$s['ultima_pedido_at']}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
