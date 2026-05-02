<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();

function printTable($db, $table) {
    echo "--- TABLE: $table ---\n";
    try {
        $res = $db->query("PRAGMA table_info($table)")->fetchAll();
        foreach ($res as $col) {
            echo "{$col['name']} ({$col['type']})\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

printTable($db, 'usuarios');
printTable($db, 'roles');
printTable($db, 'sedes');
?>
