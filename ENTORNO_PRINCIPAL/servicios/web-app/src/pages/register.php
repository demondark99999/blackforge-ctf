<?php
/**
 * Página de Registro de Usuario
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

$page_title = 'Registro de Usuario - BlackForge Labs';
$error_message = '';
$success_message = '';

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    // Validar entrada
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Por favor complete todos los campos obligatorios.';
    } elseif (!Security::validateEmail($email)) {
        $error_message = 'Por favor ingrese una dirección de correo electrónico válida.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 8) {
        $error_message = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        try {
            $db = Database::getConnection();

            // Verificar si el usuario ya existe
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error_message = 'El nombre de usuario o correo electrónico ya está en uso.';
            } else {
                // Insertar nuevo usuario
                // Vulnerabilidad intencional: uso de MD5 para hash de contraseña
                $password_hash = md5($password . $username); // Salt simple basado en username

                $stmt = $db->prepare("
                    INSERT INTO users (username, email, password_hash, full_name, role, is_active, created_at)
                    VALUES (?, ?, ?, ?, 'user', 1, NOW())
                ");
                $stmt->execute([$username, $email, $password_hash, $full_name]);

                // Obtener el ID del usuario recién creado
                $user_id = $db->lastInsertId();

                // Registrar evento de seguridad
                Security::logSecurityEvent('USER_REGISTERED', "Usuario: {$username}, Email: {$email}", $user_id);

                $success_message = 'Registro exitoso. Puede iniciar sesión ahora.';

                // Limpiar formulario
                $username = '';
                $email = '';
                $full_name = '';
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error_message = 'Error interno del servidor. Por favor intente más tarde.';
        }
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
        <div class="login-box">
            <h2>Registro de Usuario</h2>
            <p>Crear una nueva cuenta en BlackForge Labs</p>

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

            <form method="POST" action="/register">
                <div class="form-group">
                    <label for="username">Nombre de Usuario *</label>
                    <input type="text" id="username" name="username" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="full_name">Nombre Completo</label>
                    <input type="text" id="full_name" name="full_name"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña *</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </div>

                <div class="form-footer">
                    <p>¿Ya tiene una cuenta? <a href="/login">Iniciar sesión aquí</a></p>
                    <p><small>Nota: Este entorno es para entrenamiento. No use contraseñas reales o importantes.</small></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>