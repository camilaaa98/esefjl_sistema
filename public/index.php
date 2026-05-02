<?php
// 1. Configuración de errores GLOBAL y Buffer de salida
ob_start();
header('Content-Type: text/html; charset=utf-8');
require_once '../app/config/config.php';

if (defined('APP_ENV') && APP_ENV === 'production') {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// 2. Cargar Dependencias
require_once '../app/Middleware/SecurityHeaders.php';
require_once '../app/Middleware/SessionManager.php';
require_once '../app/Middleware/CsrfMiddleware.php';
require_once '../app/Middleware/RateLimiter.php';
require_once '../app/config/Database.php';

// 3. Aplicar Seguridad
SecurityHeaders::apply();
SessionManager::start();

// 4. Lógica de Enrutamiento Robusta
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Eliminar el nombre del script y la carpeta base para obtener la ruta virtual
$basePath = str_replace('/public/index.php', '', $scriptName);
$path = parse_url($requestUri, PHP_URL_PATH);

if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = trim($path, '/');

// Normalizar ruta raíz
if ($path === '' || $path === 'public' || $path === 'index.php') {
    $path = 'presentacion';
}

// Mapa de Rutas Virtuales
$routes = [
    'presentacion' => 'presentacion.php',
    'login' => 'auth/login.php',
    'inicio' => 'inicio.php',
    'logout' => 'AuthController@logout',
    'inventario-central' => 'inventory/inventario_central.php',
    'vencidos' => 'inventory/vencidos.php',
    'registro-entrega' => 'inventory/registro_entrega.php',
    'solicitud-municipio' => 'inventory/solicitud_municipio.php',
    'aprobacion-pedidos' => 'inventory/aprobacion_pedidos.php',
    'historial' => 'historial.php',
    'sedes' => 'admin/sedes.php',
    'admin-usuarios' => 'admin/admin_usuarios.php',
    'registro-paciente' => 'registro_paciente.php',
    'do-login' => 'AuthController@login'
];

// 5. Verificación de Autenticación
$publicRoutes = ['presentacion', 'login', 'do-login'];
if (!in_array($path, $publicRoutes) && !isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/login');
    exit();
}

// 6. Ejecución de la Ruta
if (array_key_exists($path, $routes)) {
    $target = $routes[$path];
    
    try {
        if (strpos($target, '@') !== false) {
            list($controller, $method) = explode('@', $target);
            require_once "../app/Controllers/$controller.php";
            $instance = new $controller();
            $instance->$method();
        } else {
            require_once "../resources/views/$target";
        }
    } catch (Throwable $e) {
        // Manejo de errores amigable según el tipo de petición
        if (strpos($path, 'do-') === 0 || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            ob_clean(); // Eliminar cualquier salida previa accidental
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => (defined('APP_ENV') && APP_ENV === 'production') ? 'Error interno' : $e->getMessage()
            ]);
        } else {
            http_response_code(500);
            echo "<h1>Error en el Sistema</h1><p>" . $e->getMessage() . "</p>";
        }
    }
} else {
    http_response_code(404);
    echo "<h1>404 - No encontrado</h1>";
}
?>