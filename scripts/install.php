<?php
require_once __DIR__ . '/core/Database.php';

$sqlFile = 'C:/Users/Maria/.gemini/antigravity/brain/5e0c7d69-9292-44b4-96a7-00cacbc51439/init_db.sql';
$result = Database::initialize($sqlFile);

if ($result === true) {
    echo "Base de datos inicializada correctamente en core/esefjl.db";
} else {
    echo "Error: " . $result;
}
?>
