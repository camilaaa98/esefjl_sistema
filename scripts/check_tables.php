<?php
require_once __DIR__ . '/../app/config/Database.php';
$db = Database::getInstance();
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
echo "=== TABLAS EN LA BASE DE DATOS ===\n\n";
foreach ($tables as $table) {
    echo "â€¢ {$table}\n";
}

// Verificar si existen las tablas necesarias para pedidos
$required = ['pedidos_municipios', 'detalles_pedido_municipio'];
echo "\n=== VERIFICACIí“N DE TABLAS DE PEDIDOS ===\n";
foreach ($required as $table) {
    if (in_array($table, $tables)) {
        echo "✅ {$table} existe\n";
    } else {
        echo "âŒ {$table} NO existe - necesita crearse\n";
    }
}
