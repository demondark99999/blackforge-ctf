<?php
/**
 * Página de Subida de Archivos
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

// Verificar autenticación
if (!Security::isAuthenticated()) {
    header('Location: /login');
    exit;
}

$page_title = 'Subir Archivo - BlackForge Labs';
$error_message = '';
$success_message = '';
$uploaded_files = [];

// Configuración de subida
$UPLOAD_DIR = __DIR__ . '/../../uploads';
$MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
$ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx', 'zip'];

// Asegurar que el directorio de uploads existe
if (!is_dir($UPLOAD_DIR)) {
    mkdir($UPLOAD_DIR, 0755, true);
}

// Procesar formulario de subida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Verificar que se subió un archivo
    if ($file['error'] === UPLOAD_ERR_OK) {
        $original_name = $file['name'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];

        // Verificar tamaño
        if ($file_size > $MAX_FILE_SIZE) {
            $error_message = 'El archivo es demasiado grande. Tamaño máximo: ' . ($MAX_FILE_SIZE / 1024 / 1024) . 'MB';
        } else {
            // Verificar extensión
            $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            if (!in_array($extension, $ALLOWED_EXTENSIONS)) {
                $error_message = 'Extensión de archivo no permitida. Extensiones permitidas: ' . implode(', ', $ALLOWED_EXTENSIONS);
            } else {
                // Generar nombre único para evitar colisiones
                $file_hash = md5_file($file_tmp);
                $unique_name = $file_hash . '_' . time() . '.' . $extension;
                $destination = $UPLOAD_DIR . '/' . $unique_name;

                // Mover archivo subi
                if (move_uploaded_file($file_tmp, $destination)) {
                    // Vulnerabilidad intencional: falta de validación de contenido real vs extensión
                    // Un archivo .jpg podría contener código PHP, por ejemplo

                    // Guardar en base de datos
                    try {
                        $db = Database::getConnection();
                        $stmt = $db->prepare("
                            INSERT INTO uploads (user_id, filename, original_name, mime_type, file_size, upload_path, is_public, scanned)
                            VALUES (?, ?, ?, ?, ?, ?, 0, 0)
                        ");
                        $mime_type = mime_content_type($destination);
                        $stmt->execute([
                            Security::getUserId(),
                            $unique_name,
                            $original_name,
                            $mime_type,
                            $file_size,
                            '/uploads/' . $unique_name
                        ]);

                        // Registrar evento de seguridad
                        Security::logSecurityEvent('FILE_UPLOAD', "Archivo: {$original_name} ({$extension})", Security::getUserId());

                        $success_message = 'Archivo subido exitosamente.';
                        $uploaded_files[] = [
                            'name' => $original_name,
                            'size' => $file_size,
                            'extension' => $extension,
                            'path' => '/uploads/' . $unique_name
                        ];
                    } catch (Exception $e) {
                        error_log("Database error during file upload: " . $e->getMessage());
                        $error_message = 'Error al guardar la información del archivo en la base de datos.';
                        // Eliminar el archivo subido si falló la inserción en BD
                        unlink($destination);
                    }
                } else {
                    $error_message = 'Error al subir el archivo.';
                }
            }
        }
    } else {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message = 'El archivo es demasiado grande.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message = 'El archivo fue subido parcialmente.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message = 'No se subió ningún archivo.';
                break;
            default:
                $error_message = 'Error desconocido al subir el archivo.';
        }
    }
}

// Obtener lista de archivos subidos por el usuario
try {
    $db = Database::getConnection();
    $stmt = $db->prepare("
        SELECT id, original_name, file_size, upload_date, is_public
        FROM uploads
        WHERE user_id = ?
        ORDER BY upload_date DESC
        LIMIT 20
    ");
    $stmt->execute([Security::getUserId()]);
    $user_files = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching user files: " . $e->getMessage());
    $user_files = [];
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
            <h1>Subir Archivo</h1>
            <nav>
                <a href="/">Inicio</a>
                <a href="/dashboard">Panel de Control</a>
                <a href="/logout">Cerrar Sesión</a>
            </nav>
        </header>

        <main>
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

            <div class="upload-section">
                <h2>Subir Nuevo Archivo</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Seleccionar Archivo</label>
                        <input type="file" id="file" name="file" required>
                        <small>Extensiones permitidas: <?= implode(', ', $ALLOWED_EXTENSIONS) ?>. Tamaño máximo: 10MB</small>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción (opcional)</label>
                        <textarea id="description" name="description" rows="3"></small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Subir Archivo</button>
                    </div>
                </form>
            </div>

            <div class="files-section">
                <h2>Mis Archivos Subidos</h2>
                <?php if (empty($user_files)): ?>
                    <p>Aún no has subido ningún archivo.</p>
                <?php else: ?>
                    <div class="files-grid">
                        <?php foreach ($user_files as $file): ?>
                            <div class="file-item">
                                <div class="file-icon">
                                    <?php
                                    // Determinar ícono basado en extensión
                                    $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                    switch ($ext) {
                                        case 'jpg':
                                        case 'jpeg':
                                        case 'png':
                                        case 'gif':
                                            echo '���������🖼������️';
                                            break;
                                        case 'pdf':
                                            echo '���������📄';
                                            break;
                                        case 'doc':
                                        case 'docx':
                                            echo '���������📝';
                                            break;
                                        case 'txt':
                                            echo '���������📃';
                                            break;
                                        case 'zip':
                                        case 'rar':
                                            echo '���������📦';
                                            break;
                                        default:
                                            echo '���������📎';
                                    }
                                    ?>
                                </div>
                                <div class="file-info">
                                    <h4><?= htmlspecialchars($file['original_name']) ?></h4>
                                    <p>
                                        <small><?= round($file['file_size'] / 1024, 1) ?> KB •
                                        Subido: <?= htmlspecialchars($file['upload_date']) ?></small>
                                    </p>
                                    <?php if (!$file['is_public']): ?>
                                        <span class="badge badge-private">Privado</span>
                                    <?php endif; ?>
                                    <div class="file-actions">
                                        <a href="<?= htmlspecialchars($file['upload_path']) ?>" target="_blank" class="btn-action">Ver</a>
                                        <a href="#" class="btn-action btn-small" onclick="if(confirm('¿Está seguro de que quiere eliminar este archivo?')){/* Lógica de eliminación iría aquí */}">Eliminar</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> BlackForge Labs. Todos los derechos reservados.</p>
            <p><small>Nota de seguridad: Este entorno contiene vulnerabilidades intencionales para entrenamiento.
                Los archivos subidos NO son escaneados para contenido malicioso en este entorno de entrenamiento.</small></p>
        </footer>
    </div>
</body>
</html>