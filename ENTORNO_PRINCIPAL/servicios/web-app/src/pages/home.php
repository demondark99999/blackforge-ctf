<?php
/**
 * Página de Inicio
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

$page_title = 'BlackForge Labs - Panel de Control';
$current_user = null;

// Verificar autenticación
if (Security::isAuthenticated()) {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT username, email, full_name, role FROM users WHERE id = ?");
        $stmt->execute([Security::getUserId()]);
        $current_user = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching user data: " . $e->getMessage());
    }
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
            <h1>BlackForge Labs - Instalación Omega</h1>
            <nav>
                <?php if (Security::isAuthenticated() && $current_user): ?>
                    <a href="/dashboard">Panel de Control</a>
                    <a href="/profile">Mi Perfil</a>
                    <?php if (Security::getUserRole() === 'admin'): ?>
                        <a href="/admin">Administración</a>
                    <?php endif; ?>
                    <a href="/logout">Cerrar Sesión (<?= htmlspecialchars($current_user['username']) ?>)</a>
                <?php else: ?>
                    <a href="/login">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </header>

        <main>
            <?php if (Security::isAuthenticated() && $current_user): ?>
                <section class="welcome">
                    <h2>Bienvenido, <?= htmlspecialchars($current_user['full_name'] ?? $current_user['username']) ?></h2>
                    <p>Acceso concedido al sistema de entrenamiento BlackForge Labs.</p>
                    <p>Fecha de acceso: <?= date('d/m/Y H:i:s') ?></p>
                </section>

                <section class="stats">
                    <h3>Estadísticas del Sistema</h3>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <h4>Usuarios Activos</h4>
                            <p><?= rand(10, 50) ?></p>
                        </div>
                        <div class="stat-item">
                            <h4>Sesiones Activas</h4>
                            <p><?= rand(5, 25) ?></p>
                        </div>
                        <div class="stat-item">
                            <h4>Archivos Subidos</h4>
                            <p><?= rand(100, 500) ?></p>
                        </div>
                        <div class="stat-item">
                            <h4>Eventos de Seguridad</h4>
                            <p><?= rand(0, 10) ?></p>
                        </div>
                    </div>
                </section>
            <?php else: ?>
                <section class="hero">
                    <h2>Acceso Restringido</h2>
                    <p>Este sistema requiere autenticación para continuar.</p>
                    <p>Utilice las credenciales proporcionadas para acceder al entorno de entrenamiento.</p>
                    <a href="/login" class="btn-primary">Iniciar Sesión</a>
                </section>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> BlackForge Labs. Todos los derechos reservados.</p>
            <p>Instalación Omega - Entorno de Entrenamiento CTF</p>
        </footer>
    </div>
</body>
</html>