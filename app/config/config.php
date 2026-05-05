<?php
/**
 * Configuración Global del Sistema - ESE Fabio Jaramillo
 * Define constantes dinámicas según el entorno (Local vs Producción)
 */

// Detectar BASE_URL dinámicamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'];
$dir = str_replace('\\', '/', dirname($scriptName));
$dir = rtrim($dir, '/');

// En Render, el proyecto suele estar en la raíz, pero en WAMP está en /YUDI_CONSTANZA/farmacia/esefjl/public
// Ajustamos para que funcione en ambos
if (strpos($dir, '/public') !== false) {
    define('BASE_URL', str_replace('/public', '', $dir));
} else {
    define('BASE_URL', $dir);
}

// Configuración de Seguridad
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutos
define('SESSION_LIFETIME', 7200); // 2 horas

// Configuración de Entorno
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'development');
}

