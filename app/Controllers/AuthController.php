<?php
// Eliminado namespace para compatibilidad con clases globales
use PDO;
use PDOException;

class AuthController
{
    public function login()
    {
        header('Content-Type: application/json');

        // 1. Verificar CSRF
        $data = json_decode(file_get_contents('php://input'), true);
        $token = $data['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            echo json_encode(['success' => false, 'message' => 'Error de seguridad: Token inválido.']);
            exit();
        }

        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Usuario y contraseña requeridos']);
            exit();
        }

        // 2. Verificar Rate Limiting
        $limit = RateLimiter::check($username);
        if (!$limit['allowed']) {
            echo json_encode(['success' => false, 'message' => "Demasiados intentos. Bloqueado por {$limit['remaining_time']} minutos."]);
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
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // í‰xito: Resetear intentos y regenerar sesión
                RateLimiter::reset($username);
                session_regenerate_id(true);
                
                $_SESSION['usuario_id'] = (int) $user['id'];
                $_SESSION['nombre'] = trim($user['nombres'] . ' ' . $user['apellidos']);
                $_SESSION['rol'] = $user['rol_nombre'];
                $_SESSION['sede'] = $user['sede_nombre'];
                $_SESSION['sede_id'] = (int) $user['sid'];
                $_SESSION['LAST_ACTIVITY'] = time();

                echo json_encode(['success' => true, 'redirect' => 'inicio']);
            } else {
                // Fallo: Registrar intento
                RateLimiter::registerAttempt($username);
                $remaining = RateLimiter::check($username)['remaining_attempts'];
                echo json_encode(['success' => false, 'message' => "Credenciales incorrectas. Intentos restantes: $remaining"]);
            }
        } catch (PDOException $e) {
            $msg = (APP_ENV === 'production') ? 'Error interno del servidor' : $e->getMessage();
            echo json_encode(['success' => false, 'message' => $msg]);
        }
        exit();
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: login');
        exit();
    }
}
