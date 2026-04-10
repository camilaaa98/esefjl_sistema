<?php
require_once __DIR__ . '/core/Database.php';
$db = Database::getInstance();

// Inspeccionar estructura real de la tabla
echo "=== COLUMNAS DE LA TABLA USUARIOS ===\n";
$cols = $db->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name='usuarios' ORDER BY ordinal_position")->fetchAll();
foreach($cols as $c) echo "  " . $c['column_name'] . " (" . $c['data_type'] . ")\n";

echo "\n=== COLUMNAS DE ROLES ===\n";
try {
    $roles = $db->query("SELECT * FROM roles")->fetchAll();
    foreach($roles as $r) { echo "  id=" . $r['id'] . " | nombre=" . $r['nombre'] . "\n"; }
} catch(Exception $e) { echo "  Error: " . $e->getMessage() . "\n"; }

echo "\n=== USUARIOS IPS ACTUALES (cualquier campo) ===\n";
$users = $db->query("SELECT * FROM usuarios LIMIT 5")->fetchAll();
if($users) echo "  Columnas: " . implode(', ', array_keys($users[0])) . "\n";

echo "\n=== BUSCANDO USUARIOS DE TIPO IPS ===\n";
$r = $db->query("SELECT u.*, s.nombre as sede_nombre FROM usuarios u LEFT JOIN sedes s ON u.sede_id = s.id LIMIT 20")->fetchAll();
foreach($r as $row) {
    echo "  " . json_encode(array_slice($row, 0, 8)) . "\n";
}

echo "\n=== SEDES ===\n";
$sedes = $db->query("SELECT id, nombre, tipo FROM sedes ORDER BY nombre")->fetchAll();
foreach($sedes as $s) echo "  ID=" . $s['id'] . " | " . $s['nombre'] . "\n";
