<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();

$updates = [
    'Acetaminofén' => 'img/productos/acetaminofen.png',
    'Loratadina' => 'img/productos/loratadina.png',
    'Solución Salina' => 'img/productos/solucion_salina.png',
    'Amoxicilina' => 'img/productos/amoxicilina.png'
];

foreach ($updates as $name => $path) {
    $db->prepare("UPDATE productos SET imagen_url = ? WHERE nombre_generico LIKE ?")
       ->execute([$path, "%$name%"]);
}

echo "Base de datos actualizada con imágenes reales.\n";
?>
