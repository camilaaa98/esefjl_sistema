<?php
require_once 'app/config/Database.php';
$db = Database::getInstance();

// Imágenes garantizadas (Unsplash Medical Collection)
$default_img = 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=150&h=150&fit=crop';
$jarabe_img = 'https://images.unsplash.com/photo-1550573105-4584e7d7a631?w=150&h=150&fit=crop';
$pastillas_img = 'https://images.unsplash.com/photo-1471864190281-a93a3070b6de?w=150&h=150&fit=crop';

$db->prepare("UPDATE productos SET imagen_url = ? WHERE nombre_generico LIKE '%Jarabe%' OR nombre_generico LIKE '%Solución%'")->execute([$jarabe_img]);
$db->prepare("UPDATE productos SET imagen_url = ? WHERE nombre_generico LIKE '%Tableta%' OR nombre_generico LIKE '%Cápsula%' OR nombre_generico LIKE '%Comprimido%'")->execute([$pastillas_img]);
$db->prepare("UPDATE productos SET imagen_url = ? WHERE imagen_url IS NULL OR imagen_url = ''")->execute([$default_img]);

echo "Medical images updated with stable high-quality URLs.";
?>
