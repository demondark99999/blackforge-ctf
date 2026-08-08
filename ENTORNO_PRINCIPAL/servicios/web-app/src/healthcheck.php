<?php
/**
 * Healthcheck para la aplicación web
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

$status = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'checks' => []
];

// Verificar conexión a base de datos
try {
    $db = Database::getConnection();
    $stmt = $db->query('SELECT 1');
    $stmt->fetch();
    $status['checks'][] = [
        'name' => 'database',
        'status' => 'ok'
    ];
} catch (Exception $e) {
    $status['status'] = 'error';
    $status['checks'][] = [
        'name' => 'database',
        'status' => 'error',
        'message' => 'Database connection failed'
    ];
}

// Verificar sesión (si es aplicable)
if (session_status() === PHP_SESSION_ACTIVE) {
    $status['checks'][] = [
        'name' => 'session',
        'status' => 'ok'
    ];
} else {
    $status['checks'][] = [
        'name' => 'session',
        'status' => 'warning',
        'message' => 'Session not started'
    ];
}

// Devolver resultado
header('Content-Type: application/json');
if ($status['status'] === 'ok') {
    http_response_code(200);
} else {
    http_response_code(503);
}
echo json_encode($status);
exit;
?>