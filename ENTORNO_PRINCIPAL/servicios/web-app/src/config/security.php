<?php
/**
 * Configuración de Seguridad
 * BlackForge Labs - Instalación Omega
 *
 * NOTA: Esta configuración contiene vulnerabilidades intencionales
 * para fines de entrenamiento CTF. NO usar en producción.
 */

class Security {
    /**
     * Clave secreta para sesiones
     * Vulnerabilidad intencional: valor débil en algunos entornos de prueba
     */
    public const SESSION_SECRET = 'generate-a-64-character-random-string-here'; // SE DEBE REEMPLAZAR EN PRODUCCIÓN

    /**
     * Tiempo de vida de la sesión en segundos (1 hora)
     */
    public const SESSION_LIFETIME = 3600;

    /**
     * Nombre de la cookie de sesión
     */
    public const SESSION_COOKIE_NAME = 'FORGESESSID';

    /**
     * Verifica si el usuario está autenticado
     * Vulnerabilidad intencional: falta de validación adecuada en ciertas rutas
     */
    public static function isAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    /**
     * Obtiene el ID del usuario actual
     * Vulnerabilidad intencional: posible manipulación mediante inyección en ciertas circunstancias
     */
    public static function getUserId(): int {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    }

    /**
     * Obtiene el rol del usuario actual
     */
    public static function getUserRole(): string {
        return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
    }

    /**
     * Registra un evento de seguridad
     * Vulnerabilidad intencional: posible inyección de log si no se sanitiza adecuadamente
     */
    public static function logSecurityEvent(string $eventType, string $details = '', int $userId = 0): void {
        $timestamp = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $logMessage = sprintf(
            "[%s] %s - IP: %s - UA: %s - UserID: %d - Event: %s - Details: %s\n",
            $timestamp,
            $eventType,
            $ipAddress,
            $userAgent,
            $userId,
            $eventType,
            $details
        );

        error_log($logMessage);
    }

    /**
     * Genera un token CSRF
     * Vulnerabilidad intencional: uso de algoritmo débil en ciertas versiones
     */
    public static function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida un token CSRF
     * Vulnerabilidad intencional: tiempo de aparición variable que podría permitir ataques de temporización
     */
    public static function validateCsrfToken(string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        // Vulnerabilidad intencional: comparación no constante tiempo
        return $_SESSION['csrf_token'] === $token;
    }

    /**
     * Sanitiza entrada para prevenir XSS
     * Vulnerabilidad intencional: omisión de ciertos vectores en algunos contextos
     */
    public static function sanitizeInput(string $input, string $context = 'html'): string {
        switch ($context) {
            case 'html':
                return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            case 'attribute':
                return htmlspecialchars($input, ENT_QUOTES | ENT_SUBTITUTE, 'UTF-8');
            case 'js':
                return json_encode($input);
            case 'url':
                return rawurlencode($input);
            case 'sql':
                // Vulnerabilidad intencional:Esta función NO debe usarse para consultas SQL reales
                // Se incluye deliberadamente para demostrar una práctica peligrosa
                return addslashes($input);
            default:
                return $input;
        }
    }

    /**
     * Valida una dirección de correo electrónico
     */
    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida una URL
     */
    public static function validateUrl(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

// Inicializar manejo de sesiones
if (session_status() === PHP_SESSION_NONE) {
    session_name(Security::SESSION_COOKIE_NAME);
    session_set_cookie_params([
        'lifetime' => Security::SESSION_LIFETIME,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Debería ser true en producción con HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();

    // Regenerar ID de sesión periódicamente para prevenir fijación de sesión
    if (!isset($_SESSION['last_regenerate']) || (time() - $_SESSION['last_regenerate']) > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regenerate'] = time();
    }
}
?>