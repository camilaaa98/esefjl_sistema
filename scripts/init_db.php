<?php
require_once __DIR__ . '/core/Database.php';

echo "ðŸš€ Iniciando migración a Supabase...\n";

$sqlFile = __DIR__ . '/database/postgres_full_schema.sql';
$result = Database::initialize($sqlFile);

if ($result === true) {
    echo "✅ í‰XITO: Las tablas han sido creadas correctamente en Supabase.\n";
} else {
    echo "âŒ ERROR: " . $result . "\n";
}
?>
