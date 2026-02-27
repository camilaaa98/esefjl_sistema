<?php
// Simulación de Login Técnico SISFARMA PRO
require_once __DIR__ . '/core/Database.php';

function simulate_login($username, $password) {
    echo "Probando acceso para: [$username] con clave: [$password]\n";
    
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.*, r.nombre AS rol_nombre, s.nombre AS sede_nombre 
            FROM usuarios u
            JOIN roles r ON u.rol_id = r.id
            JOIN sedes s ON u.sede_id = s.id
            WHERE u.username = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            return "❌ ERROR: Usuario no encontrado en la base de datos.";
        }

        if (password_verify($password, $user['password'])) {
            return "✅ ÉXITO: Autenticación confirmada. Rol: {$user['rol_nombre']} | Sede: {$user['sede_nombre']}";
        } else {
            return "❌ ERROR: Contraseña incorrecta para este usuario.";
        }
    } catch (Exception $e) {
        return "❌ ERROR DE SISTEMA: " . $e->getMessage();
    }
}

echo "--- RESULTADOS DEL TEST DE ACCESO ---\n";
echo simulate_login('jefe_solita', 'ips2026') . "\n";
echo simulate_login('admin', 'ese123') . "\n";
echo "-------------------------------------\n";
?>
