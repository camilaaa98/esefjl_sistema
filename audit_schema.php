<?php
try {
    $db = new PDO('sqlite:core/esefjl.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = ['productos', 'inventario', 'categorias', 'usuarios', 'sedes'];
    foreach ($tables as $table) {
        echo "=== SCHEMA: $table ===\n";
        $stmt = $db->query("PRAGMA table_info($table)");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo " - {$row['name']} ({$row['type']})\n";
        }
        echo "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
