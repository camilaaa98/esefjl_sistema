<?php
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $sql = file_get_contents(__DIR__ . '/database/update_alerts.sql');
    $db->exec($sql);
    echo "✅ SQL ejecutado correctamente.\n";
} catch (Exception $e) {
    echo "❌ Error SQL: " . $e->getMessage();
}
?>
