<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();
$users = $db->query("SELECT username, rol_id FROM usuarios")->fetchAll();
echo json_encode($users, JSON_PRETTY_PRINT);
?>
