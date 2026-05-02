<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();
$cols = $db->query("PRAGMA table_info(productos)")->fetchAll();
echo json_encode($cols, JSON_PRETTY_PRINT);
?>
