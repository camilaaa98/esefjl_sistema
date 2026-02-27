<?php
/**
 * Controlador de Autenticación - ESE Fabio Jaramillo
 */
session_start();
require_once __DIR__ . '/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Usuario y contraseña requeridos']);
    exit();
}

try {
    $db = Database::getInstance();
    $stmt = $db->prepare("
        SELECT u.*, r.nombre AS rol_nombre, s.nombre AS sede_nombre, s.id AS sid
        FROM usuarios u
        JOIN roles r ON u.rol_id = r.id
        JOIN sedes s ON u.sede_id = s.id
        WHERE u.username = ?
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Limpiar sesión anterior
        session_regenerate_id(true);

        // Registro de sesión estricto - siempre desde la BD
        $_SESSION['usuario_id'] = (int)$user['id'];
        $_SESSION['nombre']     = trim($user['nombres'] . ' ' . $user['apellidos']);
        $_SESSION['rol']        = $user['rol_nombre'];
        $_SESSION['sede']       = $user['sede_nombre'];
        $_SESSION['sede_id']    = (int)$user['sid'];

        echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas. Verifica tu usuario y contraseña.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
