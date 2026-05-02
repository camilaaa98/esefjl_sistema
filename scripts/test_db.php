<?php
require_once 'app/config/Database.php';

try {
    $db = Database::getInstance();
    echo "CONEXION_OK\n";
    
    $res = $db->query("SELECT COUNT(*) as total FROM usuarios")->fetch();
    echo "USUARIOS: " . $res['total'] . "\n";
    
    $res = $db->query("SELECT id, username FROM usuarios LIMIT 1")->fetch();
    echo "TEST_USER: " . ($res['username'] ?? 'N/A') . "\n";

} catch (Exception $e) {
    echo "CONEXION_ERROR: " . $e->getMessage() . "\n";
}
?>
