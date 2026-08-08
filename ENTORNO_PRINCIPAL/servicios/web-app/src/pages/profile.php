<?php
/**
 * Página de Perfil de Usuario
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

// Verificar autenticación
if (!Security::isAuthenticated()) {
    header('Location: /login');
    exit;
}

$page_title = 'Mi Perfil - BlackForge Labs';
$error_message = '';
$success_message = '';
$user_data = null;

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        $db = Database::getConnection();

        // Verificar contraseña actual si se está cambiando la contraseña
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $error_message = 'Por favor ingrese su contraseña actual para cambiarla.';
            } else {
                // Obtener hash de contraseña actual
                $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
                $stmt->execute([Security::getUserId()]);
                $user = $stmt->fetch();

                // Vulnerabilidad intencional: uso de MD5
                $current_hash = md5($current_password . Security::getUserId()); // Nota: debería ser username, pero usamos ID para variar

                if ($current_hash !== $user['password_hash']) {
                    $error_message = 'La contraseña actual es incorrecta.';
                } elseif ($new_password !== $confirm_password) {
                    $error_message = 'Las nuevas contraseñas no coinciden.';
                } elseif (strlen($new_password) < 8) {
                    $error_message = 'La nueva contraseña debe tener al menos 8 caracteres.';
                }
            }
        }

        // Si no hay errores, actualizar el perfil
        if (empty($error_message)) {
            $update_fields = [];
            $update_params = [];

            if (!empty($full_name)) {
                $update_fields[] = "full_name = ?";
                $update_params[] = $full_name;
            }

            if (!empty($email)) {
                if (!Security::validateEmail($email)) {
                    $error_message = 'Por favor ingrese una dirección de correo electrónico válida.';
                } else {
                    // Verificar que el email no esté en uso por otro usuario
                    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id <> ?");
                    $stmt->execute([$email, Security::getUserId()]);
                    if ($stmt->fetch()) {
                        $error_message = 'El correo electrónico ya está en uso por otro usuario.';
                    } else {
                        $update_fields[] = "email = ?";
                        $update_params[] = $email;
                    }
                }
            }

            if (!empty($new_password) && empty($error_message)) {
                // Vulnerabilidad intencional: uso de MD5
                $new_hash = md5($new_password . Security::getUserId());
                $update_fields[] = "password_hash = ?";
                $update_params[] = $new_hash;
            }

            if (!empty($error_message)) {
                // Ya se estableció un error arriba
            } elseif (!empty($update_fields)) {
                $update_params[] = Security::getUserId(); // Para la cláusula WHERE
                $query = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute($update_params);

                // Registrar evento de seguridad
                $action = !empty($new_password) ? 'PROFILE_UPDATE_PASSWORD' : 'PROFILE_UPDATE';
                Security::logSecurityEvent($action, "Usuario ID: " . Security::getUserId(), Security::getUserId());

                $success_message = 'Perfil actualizado exitosamente.';
            }
        }
    } catch (Exception $e) {
        error_log("Profile update error: " . $e->getMessage());
        $error_message = 'Error interno del servidor. Por favor intente más tarde.';
    }
}

// Obtener datos del usuario
try {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT id, username, email, full_name, role, created_at, last_login, failed_login_attempts FROM users WHERE id = ?");
    $stmt->execute([Security::getUserId()]);
    $user_data = $stmt->fetch();
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
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
            <h1>Mi Perfil</h1>
            <nav>
                <a href="/">Inicio</a>
                <a href="/dashboard">Panel de Control</a>
                <a href="/logout">Cerrar Sesión</a>
            </nav>
        </header>

        <main>
            <?php if (!$user_data): ?>
                <div class="alert alert-error">
                    No se pudo cargar la información del perfil.
                </div>
            <?php else: ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success_message) ?>
                    </div>
                <?php endif; ?>

                <div class="profile-card">
                    <h2>Información del Perfil</h2>
                    <div class="profile-info">
                        <div class="info-item">
                            <label>Nombre de Usuario:</label>
                            <span><?= htmlspecialchars($user_data['username']) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Correo Electrónico:</label>
                            <span><?= htmlspecialchars($user_data['email']) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Nombre Completo:</label>
                            <span><?= htmlspecialchars($user_data['full_name'] ?? 'No especificado') ?></span>
                        </div>
                        <div class="info-item">
                            <label>Rol:</label>
                            <span><?= ucfirst(htmlspecialchars($user_data['role'])) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Fecha de Registro:</label>
                            <span><?= htmlspecialchars($user_data['created_at']) ?></span>
                        </div>
                        <div class="info-item">
                            <label>Último Acceso:</label>
                            <span><?= htmlspecialchars($user_data['last_login'] ?? 'Nunca') ?></span>
                        </div>
                        <div class="info-item">
                            <label>Intentos Fallidos:</label>
                            <span><?= htmlspecialchars($user_data['failed_login_attempts']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="profile-card">
                    <h2>Actualizar Perfil</h2>
                    <form method="POST" action="/profile">
                        <div class="form-group">
                            <label for="full_name">Nombre Completo</label>
                            <input type="text" id="full_name" name="full_name"
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? $user_data['full_name'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? $user_data['email']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="current_password">Contraseña Actual</label>
                            <input type="password" id="current_password" name="current_password"
                                   placeholder="Deje en blanco si no desea cambiar la contraseña">
                            <small>Requerido si desea cambiar la contraseña</small>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Nueva Contraseña</label>
                            <input type="password" id="new_password" name="new_password"
                                   placeholder="Deje en blanco si no desea cambiar la contraseña">
                            <small>Mínimo 8 caracteres</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmar Nueva Contraseña</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   placeholder="Deje en blanco si no desea cambiar la contraseña">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                            <a href="/profile" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> BlackForge Labs. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>