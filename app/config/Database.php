<?php
/**
 * Conexión Centralizada a Base de Datos - ESE Fabio Jaramillo
 * Utiliza PDO para garantizar seguridad y portabilidad.
 */
class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        try {
            // 1. Intentar conexión a la Nube (Supabase / Render)
            $dbUrl = getenv('DATABASE_URL');
            
            // Si no hay variable de entorno, buscamos un archivo config local opcional
            if (!$dbUrl && file_exists(__DIR__ . '/config.php')) {
                require_once __DIR__ . '/config.php';
                if (defined('DATABASE_URL')) $dbUrl = DATABASE_URL;
            }

            if ($dbUrl) {
                // Verificar si el driver pgsql está disponible en PDO
                if (!in_array('pgsql', PDO::getAvailableDrivers())) {
                    throw new Exception("La extensión 'pdo_pgsql' no está habilitada en PHP.");
                }

                $parts = parse_url($dbUrl);
                $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s;sslmode=require", 
                    $parts['host'], 
                    $parts['port'] ?? 5432, 
                    ltrim($parts['path'], '/')
                );
                $this->db = new PDO($dsn, $parts['user'], $parts['pass']);
                $this->db->exec("SET client_encoding TO 'UTF8'");
            } else {
                // FALLBACK: SQLite
                $path = __DIR__ . '/../../database/esefjl.db';
                $this->db = new PDO("sqlite:" . $path);
            }
            
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // No morir con HTML, lanzar excepción
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance->db;
    }

    public static function initialize($sqlFile) {
        $db = self::getInstance();
        $sql = file_get_contents($sqlFile);
        
        try {
            $db->exec($sql);
            return true;
        } catch (PDOException $e) {
            return "Error al inicializar esquemas: " . $e->getMessage();
        }
    }
}

