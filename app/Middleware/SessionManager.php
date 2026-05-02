<?php
/**
 * SessionManager - ESE Fabio Jaramillo
 * Configuración segura de cookies y control de expiración de sesión.
 */
class SessionManager {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            
            session_set_cookie_params([
                'lifetime' => 7200, // 2 horas
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
            session_name('ESEFJL_SID');
            session_start();
        }

        // Control de expiración por inactividad
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) {
            session_unset();
            session_destroy();
            header("Location: login?timeout=1");
            exit();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

