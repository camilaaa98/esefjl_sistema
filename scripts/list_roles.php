<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();
$roles = $db->query("SELECT * FROM roles")->fetchAll();
echo json_encode($roles, JSON_PRETTY_PRINT);
?>
