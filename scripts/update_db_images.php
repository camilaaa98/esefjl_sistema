<?php
require 'app/config/Database.php';
$db = Database::getInstance();

$updates = [
    // Aciclovir Tabletas
    "UPDATE productos SET imagen_url = 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&q=80&w=400' 
     WHERE nombre_generico LIKE '%aciclovir%' AND (nombre_generico NOT LIKE '%crema%' AND nombre_generico NOT LIKE '%ungüento%')",
     
    // Aciclovir Crema
    "UPDATE productos SET imagen_url = 'https://images.unsplash.com/photo-1584017911766-d451b3d0e843?auto=format&fit=crop&q=80&w=400' 
     WHERE nombre_generico LIKE '%aciclovir%' AND (nombre_generico LIKE '%crema%' OR nombre_generico LIKE '%ungüento%')",
     
    // Acetaminofen
    "UPDATE productos SET imagen_url = 'https://images.unsplash.com/photo-1628771065518-0d82f1938462?auto=format&fit=crop&q=80&w=400' 
     WHERE nombre_generico LIKE '%acetaminofen%'",
     
    // Loratadina
    "UPDATE productos SET imagen_url = 'https://images.unsplash.com/photo-1577401239170-897942555fb3?auto=format&fit=crop&q=80&w=400' 
     WHERE nombre_generico LIKE '%loratadina%'",
     
    // Amoxicilina
    "UPDATE productos SET imagen_url = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=400' 
     WHERE nombre_generico LIKE '%amoxicilina%'",
     
    // Solucion Salina
    "UPDATE productos SET imagen_url = 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&q=80&w=400' 
     WHERE nombre_generico LIKE '%salina%'",
];

foreach ($updates as $sql) {
    $db->exec($sql);
    echo "Executed: " . substr($sql, 0, 50) . "...\n";
}
echo "Database images updated successfully.\n";
?>
