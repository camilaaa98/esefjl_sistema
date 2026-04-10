<?php
try {
    $db = new PDO('sqlite:core/esefjl.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check columns of table 'usuarios'
    echo "=== COLUMNAS USUARIOS ===\n";
    $stmt = $db->query("PRAGMA table_info(usuarios)");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['name']} ({$row['type']})\n";
    }
    
    // List users
    echo "\n=== LISTA USUARIOS ===\n";
    $users = $db->query("SELECT id, username, password, rol_id, sede_id FROM usuarios LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    foreach($users as $u) {
        echo "ID: {$u['id']} | User: {$u['username']} | Pass: " . substr($u['password'] ?? 'N/A', 0, 10) . "...\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
