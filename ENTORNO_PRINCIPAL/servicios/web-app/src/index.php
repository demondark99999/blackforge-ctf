<?php
/**
 * BlackForge Labs - Instalación Omega
 * Aplicación Web Principal
 *
 * Esta aplicación simula un sistema de gestión de contenido
 * con múltiples vulnerabilidades intencionales para entrenamiento CTF
 */

session_start();

// Configuración básica
define('BASE_PATH', __DIR__);
define('DB_HOST', getenv('DB_HOST') ?: 'database');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_NAME', getenv('DB_NAME') ?: 'forge_db');
define('DB_USER', getenv('DB_USER') ?: 'forge_user');
define('DB_PASS', getenv('DB_PASSWORD') ?: '');
define('REDIS_HOST', getenv('REDIS_HOST') ?: 'cache');
define('REDIS_PORT', getenv('REDIS_PORT') ?: 6379);

// Incluir archivos de configuración
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';

// Inicializar manejo de errores
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Enrutamiento básico
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rutas principales
switch ($uri) {
    case '/':
    case '/index.php':
        require_once BASE_PATH . '/pages/home.php';
        break;

    case '/login':
        require_once BASE_PATH . '/pages/login.php';
        break;

    case '/dashboard':
        require_once BASE_PATH . '/pages/dashboard.php';
        break;

    case '/logout':
        // Destruir sesión y redirigir
        session_destroy();
        header('Location: /login');
        exit;

    case '/healthz':
        // Endpoint de healthcheck
        http_response_code(200);
        echo 'OK';
        exit;

    default:
        // Página no encontrada
        http_response_code(404);
        require_once BASE_PATH . '/pages/404.php';
        break;
}
?>