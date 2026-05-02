<?php
/**
 * RateLimiter - ESE Fabio Jaramillo
 * Control de intentos de inicio de sesión para prevenir ataques de fuerza bruta.
 */
class RateLimiter {
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 900; // 15 minutos en segundos

    public static function check($username) {
        $key = "login_attempts_" . md5($username);
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'last_attempt' => 0];

        if ($attempts['count'] >= self::MAX_ATTEMPTS) {
            $timeRemaining = self::LOCKOUT_TIME - (time() - $attempts['last_attempt']);
            if ($timeRemaining > 0) {
                return [
                    'allowed' => false,
                    'remaining_time' => ceil($timeRemaining / 60)
                ];
            } else {
                // Resetear después del tiempo de bloqueo
                self::reset($username);
            }
        }

        return ['allowed' => true, 'remaining_attempts' => self::MAX_ATTEMPTS - $attempts['count']];
    }

    public static function registerAttempt($username) {
        $key = "login_attempts_" . md5($username);
        $attempts = $_SESSION[$key] ?? ['count' => 0, 'last_attempt' => 0];
        $attempts['count']++;
        $attempts['last_attempt'] = time();
        $_SESSION[$key] = $attempts;
    }

    public static function reset($username) {
        $key = "login_attempts_" . md5($username);
        unset($_SESSION[$key]);
    }
}

