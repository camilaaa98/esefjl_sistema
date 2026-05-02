<?php
$file = 'resources/views/presentacion.php';
$content = file_get_contents($file);

// Detectar si hay doble encoding
if (strpos($content, 'Ã³') !== false || strpos($content, 'Ã¡') !== false) {
    // Intentar revertir el doble encoding
    $fixed = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $content);
    if ($fixed) {
        file_put_contents($file, $fixed);
        echo "Fixed double encoding in $file\n";
    } else {
        echo "Failed to fix double encoding in $file\n";
    }
} else {
    echo "No double encoding detected in $file\n";
}
?>
