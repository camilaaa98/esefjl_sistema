<?php
require 'app/config/Database.php';
$db = Database::getInstance();
$stmt = $db->query("SELECT nombre_generico, imagen_url FROM productos WHERE nombre_generico LIKE '%aciclovir%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
