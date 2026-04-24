<?php
require_once 'core/Database.php';
$db = Database::getInstance();
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
foreach ($tables as $table) {
    echo "Table: " . $table['name'] . "\n";
    $cols = $db->query("PRAGMA table_info(" . $table['name'] . ")")->fetchAll();
    foreach ($cols as $col) {
        echo "  - " . $col['name'] . " (" . $col['type'] . ")\n";
    }
    echo "\n";
}
