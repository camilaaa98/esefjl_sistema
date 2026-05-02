<?php
/**
 * CsrfMiddleware - ESE Fabio Jaramillo
 * Generación y verificación de tokens CSRF para protección de formularios.
 */
class CsrfMiddleware {
    
    public static function getToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field() {
        $token = self::getToken();
        return "<input type='hidden' name='csrf_token' value='$token'>";
    }

    public static function verify() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                http_response_code(403);
                die("Error de Seguridad: Token CSRF inválido o expirado.");
            }
            // Rotar token después de uso exitoso si es necesario (opcional según el flujo)
            // self::rotate(); 
        }
    }

    public static function rotate() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

