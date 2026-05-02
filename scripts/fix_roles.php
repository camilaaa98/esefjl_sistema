<?php
/**
 * Corrección de Roles ESEFJL
 * Mueve a los Jefes de IPS al rol restringido "IPS (Municipio)"
 */
require_once 'app/config/Database.php';
$db = Database::getInstance();

// 1. Obtener el ID del rol IPS (Municipio)
$rol_ips_id = $db->query("SELECT id FROM roles WHERE nombre = 'IPS (Municipio)'")->fetchColumn();

if ($rol_ips_id) {
    // 2. Actualizar a todos los usuarios que empiezan por 'jefe_' al rol de IPS
    $stmt = $db->prepare("UPDATE usuarios SET rol_id = ? WHERE username LIKE 'jefe_%'");
    $stmt->execute([$rol_ips_id]);
    
    echo "Actualización completada: Los usuarios 'jefe_...' ahora están restringidos a su sede.";
} else {
    echo "Error: No se encontró el rol 'IPS (Municipio)'.";
}
?>
