<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();
$users = $db->query("SELECT username FROM usuarios")->fetchAll();
echo json_encode($users, JSON_PRETTY_PRINT);
?>
