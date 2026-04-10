<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    
    // Check columns of table 'usuarios'
    echo "=== COLUMNAS USUARIOS ===\n";
    $cols = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'usuarios'")->fetchAll(PDO::FETCH_COLUMN);
    print_r($cols);
    
    // Check users
    echo "\n=== LISTA USUARIOS ===\n";
    $users = $db->query("SELECT * FROM usuarios LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    foreach($users as $u) {
        $loginField = isset($u['username']) ? 'username' : (isset($u['usuario']) ? 'usuario' : 'N/A');
        $passField = isset($u['password']) ? 'password' : (isset($u['contrasena']) ? 'contrasena' : 'N/A');
        echo "ID: {$u['id']} | Login ($loginField): " . ($u[$loginField] ?? 'N/A') . " | Pass ($passField): " . ($u[$passField] ?? 'N/A') . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
