<?php
require_once __DIR__ . '/core/Database.php';
try {
    $db = Database::getInstance();
    $roles = $db->query("SELECT id, nombre FROM roles")->fetchAll();
    echo "ðŸŽ­ ROLES EN DB:\n";
    foreach($roles as $r) echo "- [{$r['id']}] '{$r['nombre']}'\n";

    $sedes = $db->query("SELECT id, nombre, tipo FROM sedes")->fetchAll();
    echo "\nðŸ¢ SEDES EN DB:\n";
    foreach($sedes as $s) echo "- [{$s['id']}] '{$s['nombre']}' ({$s['tipo']})\n";
} catch (Exception $e) {
    echo "âŒ Error: " . $e->getMessage();
}
?>
