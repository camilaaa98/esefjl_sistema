<?php
require_once 'app/config/Database.php';

$db = Database::getInstance();

$nombres = ['Juan', 'Maria', 'Carlos', 'Ana', 'Luis', 'Luisa', 'Pedro', 'Marta', 'Jorge', 'Elena', 'Diego', 'Lucia', 'Roberto', 'Sofia', 'Andres', 'Paula', 'Fernando', 'Carmen', 'Ricardo', 'Isabel'];
$apellidos = ['Garcia', 'Rodriguez', 'Lopez', 'Martinez', 'Sanchez', 'Perez', 'Gomez', 'Martin', 'Jimenez', 'Ruiz', 'Hernandez', 'Diaz', 'Moreno', 'Muñoz', 'Alvarez', 'Romero', 'Alonso', 'Gutierrez', 'Navarro', 'Torres'];

echo "Generando 500 usuarios...\n";

$db->beginTransaction();

try {
    for ($i = 0; $i < 500; $i++) {
        $nombre = $nombres[array_rand($nombres)];
        $apellido = $apellidos[array_rand($apellidos)];
        $username = strtolower($nombre . "." . $apellido . rand(100, 999));
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $documento = rand(10000000, 99999999);
        
        // Distribución: 
        // 1-5: Gerentes (1%)
        // 6-30: Regentes (5%)
        // 31-80: Admins (10%)
        // 81-500: IPS (84%)
        
        $rand = rand(1, 100);
        if ($rand <= 1) $rol_id = 1;
        elseif ($rand <= 6) $rol_id = 2;
        elseif ($rand <= 16) $rol_id = 3;
        else $rol_id = 4;
        
        // Sedes: 1-6 (1 es Florencia, el resto municipios)
        if ($rol_id == 4) {
            $sede_id = rand(2, 6); // Municipios
        } else {
            $sede_id = 1; // Florencia
        }

        $stmt = $db->prepare("INSERT INTO usuarios (username, password, nombres, apellidos, documento, rol_id, sede_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $nombre, $apellido, $documento, $rol_id, $sede_id]);
    }
    $db->commit();
    echo "Â¡í‰xito! 500 usuarios creados y distribuidos.\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
