<?php
/**
 * Página de Inicio de Sesión
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

$page_title = 'Iniciar Sesión - BlackForge Labs';
$error_message = '';
$success_message = '';

// Procesar formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validar entrada
    if (empty($username) || empty($password)) {
        $error_message = 'Por favor ingrese nombre de usuario y contraseña.';
    } else {
        try {
            $db = Database::getConnection();

            // Consulta vulnerable a inyección SQL intencionalmente para entrenamiento CTF
            // En una aplicación real, se usarían consultas preparadas
            $sql = "SELECT id, username, password_hash, email, full_name, role, is_active
                    FROM users
                    WHERE username = '$username'
                    AND is_active = 1";

            $stmt = $db->query($sql);
            $user = $stmt->fetch();

            if ($user) {
                // Verificar contraseña (en un sistema real se usaría password_verify)
                // Aquí simulamos una verificación con hash simple para el ejercicio
                $hashed_password = md5($password . $user['username']); // Vulnerabilidad intencional: uso de MD5

                if ($hashed_password === $user['password_hash']) {
                    // Inicio de sesión exitoso
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];

                    // Registrar evento de seguridad
                    Security::logSecurityEvent('LOGIN_SUCCESS', "Usuario: {$username}", $user['id']);

                    // Redirigir al panel de control
                    header('Location: /dashboard');
                    exit;
                } else {
                    // Contraseña incorrecta
                    $error_message = 'Nombre de usuario o contraseña incorrectos.';

                    // Registrar intento fallido
                    Security::logSecurityEvent('LOGIN_FAILED', "Usuario: {$username} - Contraseña incorrecta");
                }
            } else {
                // Usuario no encontrado
                $error_message = 'Nombre de usuario o contraseña incorrectos.';

                // Registrar intento fallido (evitar enumeración de usuarios)
                Security::logSecurityEvent('LOGIN_FAILED', "Intento de inicio de sesión con usuario inexistente");
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error_message = 'Error interno del servidor. Por favor intente más tarde.';
        }
    }
}

// Mostrar mensaje de salida si existe
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $success_message = 'Ha cerrado sesión exitosamente.';
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
    <div class="container login-container">
        <div class="login-box">
            <h2>BlackForge Labs</h2>
            <p>Instalación Omega - Sistema de Entrenamiento</p>

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

            <form method="POST" action="/login">
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </div>

                <div class="form-footer">
                    <p>¿Olvidó su contraseña? <a href="/recovery">Restablecer aquí</a></p>
                    <p>¿Necesita una cuenta? <a href="/register">Registrarse</a></p>
                </div>
            </form>
        </div>

        <div class="login-info">
            <h3>Sobre este Sistema</h3>
            <p>Este entorno está diseñado para entrenamiento en ciberseguridad avanzada.
               Contiene vulnerabilidades intencionales para practicar técnicas de
               explotación y defensa.</p>
            <p><strong>Advertencia:</strong> Este sistema NO debe utilizarse para
               actividades maliciosas ni contra sistemas sin autorización explícita.</p>
        </div>
    </div>
</body>
</html>