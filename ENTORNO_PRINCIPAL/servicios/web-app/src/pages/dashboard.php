<?php
/**
 * Panel de Control
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

// Verificar autenticación
if (!Security::isAuthenticated()) {
    header('Location: /login');
    exit;
}

$page_title = 'Panel de Control - BlackForge Labs';
$current_user = null;
$user_stats = [];

try {
    $db = Database::getConnection();

    // Obtener información del usuario
    $stmt = $db->prepare("SELECT username, email, full_name, role, created_at, last_login FROM users WHERE id = ?");
    $stmt->execute([Security::getUserId()]);
    $current_user = $stmt->fetch();

    // Obtener estadísticas del usuario
    $stmt = $db->prepare("
        SELECT
            (SELECT COUNT(*) FROM uploads WHERE user_id = ?) as uploads_count,
            (SELECT COUNT(*) FROM comments WHERE user_id = ?) as comments_count,
            (SELECT COUNT(*) FROM log_entries WHERE user_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 DAY)) as recent_logins
    ");
    $stmt->execute([Security::getUserId(), Security::getUserId(), Security::getUserId()]);
    $user_stats = $stmt->fetch();

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Panel de Control</h1>
            <nav>
                <a href="/">Inicio</a>
                <a href="/profile">Mi Perfil</a>
                <?php if (Security::getUserRole() === 'admin'): ?>
                    <a href="/admin">Administración</a>
                <?php endif; ?>
                <a href="/logout">Cerrar Sesión</a>
            </nav>
        </header>

        <main>
            <div class="dashboard-header">
                <h2>Bienvenido, <?= htmlspecialchars($current_user['full_name'] ?? $current_user['username']) ?></h2>
                <p>Rol: <?= ucfirst(htmlspecialchars($current_user['role'])) ?> | Último acceso: <?= htmlspecialchars($current_user['last_login'] ?? 'Nunca') ?></p>
            </div>

            <div class="dashboard-grid">
                <div class="widget">
                    <h3>Mis Actividades</h3>
                    <ul class="activity-list">
                        <li>
                            <span class="activity-icon">���📁</span>
                            <div>
                                <h4>Archivos Subidos</h4>
                                <p><?= number_format($user_stats['uploads_count'] ?? 0) ?> archivos</p>
                            </div>
                        </li>
                        <li>
                            <span class="activity-icon">���💬</span>
                            <div>
                                <h4>Comentarios Realizados</h4>
                                <p><?= number_format($user_stats['comments_count'] ?? 0) ?> comentarios</p>
                            </div>
                        </li>
                        <li>
                            <span class="activity-icon">���🕒</span>
                            <div>
                                <h4>Accesos Recientes</h4>
                                <p><?= number_format($user_stats['recent_logins'] ?? 0) ?> en las últimas 24h</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="widget">
                    <h3>Sistema de Mensajes</h3>
                    <div class="messages-list">
                        <div class="message alert-info">
                            <strong>Sistema:</strong> Bienvenido al entorno de entrenamiento BlackForge Labs.
                        </div>
                        <div class="message alert-warning">
                            <strong>Seguridad:</strong> Recuerde que todas las acciones están siendo registradas para fines de auditoría.
                        </div>
                        <?php if ($current_user['role'] === 'admin'): ?>
                            <div class="message alert-success">
                                <strong>Administrador:</strong> Tiene acceso completo al panel de administración.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="widget">
                    <h3>Accesos Rápidos</h3>
                    <div class="quick-actions">
                        <a href="/upload" class="btn-action">
                            <span>���📤</span> Subir Archivo
                        </a>
                        <a href="/gallery" class="btn-action">
                            <span>���🖼��️</span> Ver Galería
                        </a>
                        <a href="/settings" class="btn-action">
                            <span>��⚙��️</span> Configuración
                        </a>
                        <?php if ($current_user['role'] === 'admin'): ?>
                            <a href="/admin/users" class="btn-action">
                                <span>���👥</span> Gestionar Usuarios
                            </a>
                            <a href="/admin/logs" class="btn-action">
                                <span>���📋</span> Ver Logs
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="widget">
                    <h3>Estado del Sistema</h3>
                    <div class="system-status">
                        <div class="status-item">
                            <label>Servidor Web:</label>
                            <span class="status-indicator status-ok">Operativo</span>
                        </div>
                        <div class="status-item">
                            <label>Base de Datos:</label>
                            <span class="status-indicator status-ok">Conectado</span>
                        </div>
                        <div class="status-item">
                            <label>Servicio de Cache:</label>
                            <span class="status-indicator status-ok">Disponible</span>
                        </div>
                        <div class="status-item">
                            <label>Cola de Trabajos:</label>
                            <span class="status-indicator status-ok">Normal</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> BlackForge Labs. Todos los derechos reservados.</p>
            <p>Session ID: <?= session_id() ?> | IP: <?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'unknown') ?></p>
        </footer>
    </div>
</body>
</html>