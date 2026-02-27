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
                // Verificar si la extensión pgsql está cargada
                if (!extension_loaded('pdo_pgsql')) {
                    throw new Exception("La extensión 'pdo_pgsql' no está habilitada en PHP. Por favor, actívala en tu panel de WAMP.");
                }

                $parts = parse_url($dbUrl);
                $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s;sslmode=require", 
                    $parts['host'], 
                    $parts['port'] ?? 5432, 
                    ltrim($parts['path'], '/')
                );
                $this->db = new PDO($dsn, $parts['user'], $parts['pass']);
            } else {
                // FALLBACK: SQLite para desarrollo rápido si no hay Postgres configurado
                $path = __DIR__ . '/esefjl.db';
                $this->db = new PDO("sqlite:" . $path);
            }
            
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Error amigable para el usuario
            die("<div style='background:#fdecea; color:#b71c1c; padding:20px; border-radius:10px; border:1px solid #ef9a9a; font-family:sans-serif;'>
                    <h3 style='margin-top:0;'>⚠️ Fallo en Conexión a Base de Datos</h3>
                    <p>{$e->getMessage()}</p>
                    <hr style='border:0; border-top:1px solid #ef9a9a;'>
                    <small>Sugerencia: Asegúrate de que la extensión <b>php_pdo_pgsql</b> esté marcada en WAMP -> PHP -> Extensiones.</small>
                 </div>");
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
?>
