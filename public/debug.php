<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OpCache Reset: SUCCESS<br>";
} else {
    echo "OpCache Reset: NOT AVAILABLE<br>";
}
echo "FILE CONTENT of index.php:<pre>";
echo htmlspecialchars(file_get_contents(__DIR__ . '/index.php'));
echo "</pre>";
?>
