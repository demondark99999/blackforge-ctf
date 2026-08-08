<?php
/**
 * API REST Básica
 * BlackForge Labs - Instalación Omega
 *
 * Esta API proporciona endpoints básicos para interactuar con el sistema
 * Contiene vulnerabilidades intencionales para entrenamiento CTF
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

// Configuración de encabezados CORS (intencionalmente permisiva para entrenamiento)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar autenticación mediante token en header (método alternativo)
function authenticateApiRequest() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        if (preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            // Vulnerabilidad intencional: validación débil de token
            // En una implementación real, se verificaría contra una tienda de tokens segura
            if ($token === 'demo-api-token-for-training-only') {
                return true;
            }
        }
    }
    // También aceptar autenticación por sesión
    return Security::isAuthenticated();
}

// Rutas API
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('?', $_SERVER['REQUEST_URI'])[0];
$parts = explode('/', trim($request, '/'));

try {
    $db = Database::getConnection();

    // API: Obtener usuario actual (requiere autenticación)
    if ($method === 'GET' && $parts[0] === 'api' && $parts[1] === 'me') {
        if (!authenticateApiRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $stmt = $db->prepare("SELECT id, username, email, full_name, role, created_at FROM users WHERE id = ?");
        $stmt->execute([Security::getUserId()]);
        $user = $stmt->fetch();

        if ($user) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'created_at' => $user['created_at']
                ]
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
        exit;
    }

    // API: Listar archivos del usuario (requiere autenticación)
    if ($method === 'GET' && $parts[0] === 'api' && $parts[1] === 'files') {
        if (!authenticateApiRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $limit = isset($_GET['limit']) ? min(intval($_GET['limit']), 100) : 20;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

        $stmt = $db->prepare("
            SELECT id, original_name, file_size, upload_date, mime_type
            FROM uploads
            WHERE user_id = ?
            ORDER BY upload_date DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([Security::getUserId(), $limit, $offset]);
        $files = $stmt->fetchAll();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'count' => count($files),
            'data' => $files
        ]);
        exit;
    }

    // API: Estadísticas del sistema (vulnerabilidad intencional: información excesiva)
    if ($method === 'GET' && $parts[0] === 'api' && $parts[1] === 'stats') {
        // Vulnerabilidad intencional: no requiere autenticación para estadísticas sensibles
        $stats = [];

        // Estadísticas de usuarios
        $stmt = $db->query("SELECT COUNT(*) as total_users FROM users");
        $stats['users']['total'] = (int)$stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) as active_users FROM users WHERE is_active = 1");
        $stats['users']['active'] = (int)$stmt->fetchColumn();

        // Estadísticas de archivos
        $stmt = $db->query("SELECT COUNT(*) as total_files, SUM(file_size) as total_size FROM uploads");
        $file_stats = $stmt->fetch();
        $stats['files']['total'] = (int)$file_stats['total'];
        $stats['files']['total_size_bytes'] = (int)$file_stats['total_size'];

        // Estadísticas de comentarios
        $stmt = $db->query("SELECT COUNT(*) as total_comments FROM comments");
        $stats['comments']['total'] = (int)$stmt->fetchColumn();

        // Información del sistema (vulnerabilidad intencional: exposición de detalles)
        $stats['system']['php_version'] = phpversion();
        $stats['system']['server'] = $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido';
        $stats['system']['time'] = date('c');

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
        exit;
    }

    // API: Ejecutar consulta arbitraria (VULNERABILIDAD INTENCIONAL: INYECCIÓN SQL)
    if ($method === 'GET' && $parts[0] === 'api' && $parts[1] === 'query') {
        // ������ ���� ADVERTENCIA: Esta es una vulnerabilidad intencional de inyección SQL
        // NO hacer esto en una aplicación real
        if (isset($_GET['sql'])) {
            $sql = $_GET['sql'];

            // Vulnerabilidad intencional: ejecución directa de consulta proporcionada por el usuario
            try {
                $stmt = $db->query($sql);
                if ($stmt === false) {
                    throw new Exception($db->errorInfo()[2]);
                }

                // Determinar si es una consulta SELECT o modificativa
                if (preg_match('/^\s*SELECT/i', $sql)) {
                    $results = $stmt->fetchAll();
                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'row_count' => count($results),
                        'data' => $results
                    ]);
                } else {
                    $affected_rows = $stmt->rowCount();
                    http_response_code(200);
                    echo json_encode([
                        'success' => true,
                        'affected_rows' => $affected_rows,
                        'message' => 'Consulta ejecutada exitosamente'
                    ]);
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Error en la consulta SQL',
                    'message' => $e->getMessage() // Vulnerabilidad intencional: exposición de detalles de error
                ]);
            }
            exit;
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Parámetro SQL requerido'
            ]);
            exit;
        }
    }

    // Ruta no encontrada
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'Endpoint de API no encontrado'
    ]);
} catch (Exception $e) {
    error_log("API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
}
?>