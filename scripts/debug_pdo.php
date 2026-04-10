<?php
echo "Extension loaded (pdo_pgsql): " . (extension_loaded('pdo_pgsql') ? 'YES' : 'NO') . "\n";
echo "PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
?>
