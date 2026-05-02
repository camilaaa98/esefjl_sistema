<?php
require_once 'app/config/Database.php';

$db = Database::getInstance();

echo "ROLES:\n";
$roles = $db->query("SELECT id, nombre FROM roles")->fetchAll();
foreach ($roles as $r) echo "{$r['id']} - {$r['nombre']}\n";

echo "\nSEDES:\n";
$sedes = $db->query("SELECT id, nombre FROM sedes")->fetchAll();
foreach ($sedes as $s) echo "{$s['id']} - {$s['nombre']}\n";
?>
