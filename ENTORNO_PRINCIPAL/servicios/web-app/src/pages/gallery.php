<?php
/**
 * Página de Galería de Archivos
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

// Verificar autenticación
if (!Security::isAuthenticated()) {
    header('Location: /login');
    exit;
}

$page_title = 'Galería de Archivos - BlackForge Labs';
$error_message = '';
$search_query = trim($_GET['q'] ?? '');
$file_type = $_GET['type'] ?? 'all';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Obtener lista de archivos
try {
    $db = Database::getConnection();

    // Construir consulta basada en filtros
    $where_conditions = ["user_id = ?"];
    $params = [Security::getUserId()];

    if (!empty($search_query)) {
        $where_conditions[] = "original_name LIKE ?";
        $params[] = "%{$search_query}%";
    }

    if ($file_type !== 'all') {
        $where_conditions[] = "filename LIKE ?";
        $params[] = "%{$file_type}";
    }

    $where_clause = implode(' AND ', $where_conditions);

    // Contar total de archivos
    $count_query = "SELECT COUNT(*) FROM uploads WHERE {$where_clause}";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total_files = (int)$count_stmt->fetchColumn();
    $total_pages = max(1, ceil($total_files / $per_page));

    // Obtener archivos paginados
    $query = "
        SELECT id, original_name, file_size, upload_date, upload_path, is_public
        FROM uploads
        WHERE {$where_clause}
        ORDER BY upload_date DESC
        LIMIT ? OFFSET ?
    ";
    $params[] = $per_page;
    $params[] = $offset;

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $files = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching gallery files: " . $e->getMessage());
    $files = [];
    $total_files = 0;
    $total_pages = 1;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .gallery-filters {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .gallery-item {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: transform 0.2s;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
        }
        .gallery-item-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .gallery-item-info {
            padding: 1rem;
        }
        .gallery-item-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .gallery-item-meta {
            display: flex;
            justify-check: space-between;
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }
        .pagination-btn {
            padding: 0.5rem 1rem;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
        }
        .pagination-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .pagination-current {
            padding: 0.5rem 1rem;
            background-color: var(--light-color);
            border-radius: var(--border-radius);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Galería de Archivos</h1>
            <nav>
                <a href="/">Inicio</a>
                <a href="/dashboard">Panel de Control</a>
                <a href="/upload">Subir Archivo</a>
                <a href="/logout">Cerrar Sesión</a>
            </nav>
        </header>

        <main>
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <div class="gallery-filters">
                <h2>Buscar y Filtrar Archivos</h2>
                <form method="GET" action="/gallery">
                    <div class="form-group">
                        <label for="q">Buscar por nombre:</label>
                        <input type="text" id="q" name="q" value="<?= htmlspecialchars($search_query) ?>">
                    </div>

                    <div class="form-group">
                        <label for="type">Tipo de archivo:</label>
                        <select id="type" name="type">
                            <option value="all" <?= $file_type === 'all' ? 'selected' : '' ?>>Todos</option>
                            <option value="jpg" <?= $file_type === 'jpg' ? 'selected' : '' ?>>Imágenes JPG</option>
                            <option value="png" <?= $file_type === 'png' ? 'selected' : '' ?>>Imágenes PNG</option>
                            <option value="pdf" <?= $file_type === 'pdf' ? 'selected' : '' ?>>Documentos PDF</option>
                            <option value="txt" <?= $file_type === 'txt' ? 'selected' : '' ?>>Archivos TXT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="/gallery" class="btn btn-secondary">Limpiar Filtros</a>
                    </div>
                </form>
            </div>

            <div class="gallery-info">
                <p>Mostrando <?= $total_files ?> archivos (Página <?= $page ?> de <?= $total_pages ?>)</p>
            </div>

            <?php if (empty($files)): ?>
                <div class="alert alert-info">
                    No se encontraron archivos que coincidan con los criterios de búsqueda.
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($files as $file): ?>
                        <div class="gallery-item">
                            <?php
                            // Determinar qué mostrar basado en el tipo de archivo
                            $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                            $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                            ?>
                            <div class="gallery-item-image">
                                <?php if ($is_image): ?>
                                    <img src="<?= htmlspecialchars($file['upload_path']) ?>" alt="<?= htmlspecialchars($file['original_name']) ?>" onerror="this.onerror=null; this.src='/assets/images/default-image.png';">
                                <?php else: ?>
                                    <div>
                                        <?php
                                        // Mostrar ícono basado en extensión
                                        switch ($ext) {
                                            case 'pdf':
                                                echo '���������������������📄';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                echo '���������������������📝';
                                                break;
                                            case 'txt':
                                                echo '���������������������📃';
                                                break;
                                            case 'zip':
                                            case 'rar':
                                                echo '���������������������📦';
                                                break;
                                            default:
                                                echo '���������������������📎';
                                        }
                                        ?>
                                        <br>
                                        <small><?= strtoupper($ext) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="gallery-item-info">
                                <div class="gallery-item-title"><?= htmlspecialchars($file['original_name']) ?></div>
                                <div class="gallery-item-meta">
                                    <span><?= round($file['file_size'] / 1024, 1) ?> KB</span>
                                    <span><?= date('d/m/y', strtotime($file['upload_date'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <button class="pagination-btn" onclick="window.location.href='/gallery?page=<?= ($page - 1) ?>&<?= http_build_query(array_merge($_GET, ['page' => ($page - 1)])) ?>">Anterior</button>
                    <?php else: ?>
                        <button class="pagination-btn" disabled>Anterior</button>
                    <?php endif; ?>

                    <span class="pagination-current"><?= $page ?> de <?= $total_pages ?></span>

                    <?php if ($page < $total_pages): ?>
                        <button class="pagination-btn" onclick="window.location.href='/gallery?page=<?= ($page + 1) ?>&<?= http_build_query(array_merge($_GET, ['page' => ($page + 1)])) ?>">Siguiente</button>
                    <?php else: ?>
                        <button class="pagination-btn" disabled>Siguiente</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> BlackForge Labs. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>