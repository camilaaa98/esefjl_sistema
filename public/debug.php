<?php
echo "FILE CONTENT of index.php:<pre>";
echo htmlspecialchars(file_get_contents(__DIR__ . '/index.php'));
echo "</pre>";
?>
