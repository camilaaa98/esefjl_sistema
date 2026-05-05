<?php
echo "Render Debug Info:<br>";
echo "Current File: " . __FILE__ . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Config exists: " . (file_exists(__DIR__ . '/../app/config/config.php') ? 'YES' : 'NO') . "<br>";
echo "Commit: " . date('Y-m-d H:i:s') . "<br>";
?>
