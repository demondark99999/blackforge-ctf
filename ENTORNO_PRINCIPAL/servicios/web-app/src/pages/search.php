<?php
/**
 * Página de Búsqueda
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

$page_title = 'Buscar - BlackForge Labs';
$error_message = '';
$search_query = trim($_GET['q'] ?? '');
$search_type = $_GET['type'] ?? 'all';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

$results = [
    'users' => [],
    'files' => [],
    'comments' => [],
    'total' => 0
];

// Solo procesar si hay una consulta de búsqueda
if (!empty($search_query)) {
    try {
        $db = Database::getConnection();

        // Búsqueda en usuarios
        if ($search_type === 'all' || $search_type === 'users') {
            $stmt = $db->prepare("
                SELECT id, username, email, full_name, role, created_at
                FROM users
                WHERE (username LIKE ? OR email LIKE ? OR full_name LIKE ?)
                AND is_active = 1
                LIMIT ? OFFSET ?
            ");
            $search_term = "%{$search_query}%";
            $stmt->execute([$search_term, $search_term, $search_term, $per_page, $offset]);
            $results['users'] = $stmt->fetchAll();
        }

        // Búsqueda en archivos
        if ($search_type === 'all' || $search_type === 'files') {
            $stmt = $db->prepare("
                SELECT id, original_name, file_size, upload_date, upload_path
                FROM uploads
                WHERE original_name LIKE ?
                LIMIT ? OFFSET ?
            ");
            $search_term = "%{$search_query}%";
            $stmt->execute([$search_term, $per_page, $offset]);
            $results['files'] = $stmt->fetchAll();
        }

        // Búsqueda en comentarios
        if ($search_type === 'all' || $search_type === 'comments') {
            $stmt = $db->prepare("
                SELECT c.id, c.content, c.created_at, u.username
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.content LIKE ?
                LIMIT ? OFFSET ?
            ");
            $search_term = "%{$search_query}%";
            $stmt->execute([$search_term, $per_page, $offset]);
            $results['comments'] = $stmt->fetchAll();
        }

        // Contar total de resultados (aproximado para demonstration)
        // En una aplicación real, se haría una consulta COUNT por cada tipo
        $results['total'] = count($results['users']) + count($results['files']) + count($results['comments']);

    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        $error_message = 'Error al realizar la búsqueda. Por favor intente más tarde.';
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
    <div="container">
        <header>
            <h1>Buscar en BlackForge Labs</h1>
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

            <div class="search-section">
                <h2>Buscar Contenido</h2>
                <form method="GET" action="/search">
                    <div class="form-group">
                        <label for="q">¿Qué estás buscando?</label>
                        <input type="text" id="q" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Ingrese términos de búsqueda..." required>
                    </div>

                    <div class="form-group">
                        <label for="type">Tipo de contenido:</label>
                        <select id="type" name="type">
                            <option value="all" <?= $search_type === 'all' ? 'selected' : '' ?>>Todos los tipos</option>
                            <option value="users" <?= $search_type === 'users' ? 'selected' : '' ?>>Usuarios</option>
                            <option value="files" <?= $search_type === 'files' ? 'selected' : '' ?>>Archivos</option>
                            <option value="comments" <?= $search_type === 'comments' ? 'selected' : '' ?>>Comentarios</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </div>

            <?php if (empty($search_query)): ?>
                <div class="alert alert-info">
                    Ingrese términos de búsqueda para comenzar.
                </div>
            <?php elseif (empty($results['users']) && empty($results['files']) && empty($results['comments'])): ?>
                <div class="alert alert-info">
                    No se encontraron resultados para "<strong><?= htmlspecialchars($search_query) ?></strong>".
                </div>
                <p>Intente con términos diferentes o menos específicos.</p>
            <?php else: ?>
                <div class="results-header">
                    <h2>Resultados de búsqueda para "<strong><?= htmlspecialchars($search_query) ?></strong>"</h2>
                    <p><?= number_format($results['total']) ?> resultados encontrados</p>
                </div>

                <div class="results-tabs">
                    <button class="tab-btn <?= $search_type === 'users' ? 'active' : '' ?>" onclick="window.location.href='/search?q=<?= urlencode($search_query) ?>&type=users'">Usuarios (<?= count($results['users']) ?>)</button>
                    <button class="tab-btn <?= $search_type === 'files' ? 'active' : '' ?>" onclick="window.location.href='/search?q=<?= urlencode($search_query) ?>&type=files'">Archivos (<?= count($results['files']) ?>)</button>
                    <button class="tab-btn <?= $search_type === 'comments' ? 'active' : '' ?>" onclick="window.location.href='/search?q=<?= urlencode($search_query) ?>&type=comments'">Comentarios (<?= count($results['comments']) ?>)</button>
                </div>

                <?php if (!empty($results['users'])): ?>
                    <section class="results-section">
                        <h3>Usuarios</h3>
                        <div class="results-list">
                            <?php foreach ($results['users'] as $user): ?>
                                <div class="result-item">
                                    <div class="result-icon">���������👤</div>
                                    <div class="result-info">
                                        <h4><?= htmlspecialchars($user['username']) ?></h4>
                                        <p>
                                            <strong>Correo:</strong> <?= htmlspecialchars($user['email']) ?><br>
                                            <strong>Nombre completo:</strong> <?= htmlspecialchars($user['full_name'] ?? 'No especificado') ?><br>
                                            <strong>Rol:</strong> <?= ucfirst(htmlspecialchars($user['role'])) ?><br>
                                            <strong>Registro:</strong> <?= htmlspecialchars($user['created_at']) ?>
                                        </p>
                                        <?php if (Security::isAuthenticated() && Security::getUserRole() === 'admin'): ?>
                                            <a href="/admin/users/edit?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline">Editar</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($results['files'])): ?>
                    <section class="results-section">
                        <h3>Archivos</h3>
                        <div class="results-list">
                            <?php foreach ($results['files'] as $file): ?>
                                <div class="result-item">
                                    <div class="result-icon">
                                        <?php
                                        $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                        switch ($ext) {
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif':
                                                echo '�������📷';
                                                break;
                                            case 'pdf':
                                                echo '�����������������📄';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                echo '�����������������📝';
                                                break;
                                            case 'txt':
                                                echo '�����������������📃';
                                                break;
                                            default:
                                                echo '�����������������📎';
                                        }
                                        ?>
                                    </div>
                                    <div class="result-info">
                                        <h4><?= htmlspecialchars($file['original_name']) ?></h4>
                                        <p>
                                            <strong>Tamaño:</strong> <?= round($file['file_size'] / 1024, 1) ?> KB<br>
                                            <strong>Tipo:</strong> <?= strtoupper(pathinfo($file['original_name'], PATHINFO_EXTENSION)) ?><br>
                                            <strong>Subido:</strong> <?= htmlspecialchars($file['upload_date']) ?>
                                        </p>
                                        <div class="result-actions">
                                            <a href="<?= htmlspecialchars($file['upload_path']) ?>" target="_blank" class="btn btn-sm btn-outline">Ver</a>
                                            <a href="#" class="btn btn-sm btn-outline" onclick="if(confirm('¿Eliminar este archivo?')){/* Lógica de eliminación */}">Eliminar</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($results['comments'])): ?>
                    <section class="results-section">
                        <h3>Comentarios</h3>
                        <div class="results-list">
                            <?php foreach ($results['comments'] as $comment): ?>
                                <div class="result-item">
                                    <div class="result-icon">���������💬</div>
                                    <div class="result-info">
                                        <h4><?= htmlspecialchars($comment['username']) ?></h4>
                                        <p><?= nl2br(htmlspecialchars(substr($comment['content'], 0, 100))) . (strlen($comment['content']) > 100 ? '...' : '') ?></p>
                                        <p><small>Fecha: <?= htmlspecialchars($comment['created_at']) ?></small></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Paginación (simplificada) -->
                <?php if ($results['total'] > $per_page): ?>
                    <div class="pagination">
                        <p>Mostrando resultados <?= (($page - 1) * $per_page) + 1 ?> - <?= min($page * $per_page, $results['total']) ?> de <?= $results['total'] ?></p>
                        <div class="pagination-controls">
                            <?php if ($page > 1): ?>
                                <a href="/search?q=<?= urlencode($search_query) ?>&type=<?= htmlspecialchars($search_type) ?>&page=<?= ($page - 1) ?>" class="btn btn-outline">← Anterior</a>
                            <?php endif; ?>
                            <?php if (($page * $per_page) < $results['total']): ?>
                                <a href="/search?q=<?= urlencode($search_query) ?>&type=<?= htmlspecialchars($search_type) ?>&page=<?= ($page + 1) ?>" class="btn btn-outline">Siguiente →</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> BlackForge Labs. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>