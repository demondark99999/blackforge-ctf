<?php
/**
 * Plantilla Base de Página
 * BlackForge Labs - Instalación Omega
 *
 * Plantilla base para todas las páginas del sitio
 * Contiene vulnerabilidades intencionales para entrenamiento CTF
 */

/**
 * Renderiza la plantilla base de página
 * @param string $title Título de la página
 * @param string $content Contenido principal de la página
 * @param array $opts Opciones adicionales (active_page, show_sidebar, etc.)
 * @return string HTML completo de la página
 */
function renderPageTemplate(string $title, string $content, array $opts = []): string {
    // Valores predeterminados para opciones
    $active_page = $opts['active_page'] ?? '';
    $show_sidebar = $opts['show_sidebar'] ?? true;
    $show_footer = $opts['show_footer'] ?? true;
    $custom_css = $opts['custom_css'] ?? '';
    $custom_js = $opts['custom_js'] ?? '';

    // Vulnerabilidad intencional: falta de escape en $title si se usa en contexto de atributo
    // Aunque usamos htmlspecialchars para el contenido visible, en el title del browser está bien
    // Pero si se usara en algún atributo HTML sin escape, sería problema

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> - BlackForge Labs</title>
        <link rel="stylesheet" href="/assets/css/style.css">
        <?php if (!empty($custom_css)): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($custom_css) ?>">
        <?php endif; ?>
        <!-- Vincular tema activo -->
        <link rel="stylesheet" href="/src/themes/default/css/theme.css">
    </head>
    <body>
        <!-- Header -->
        <header class="site-header">
            <div class="container">
                <div class="site-branding">
                    <h1><a href="/">BlackForge Labs</a></h1>
                    <p class="site-description">Instalación Omega - Entrenamiento CTF Avanzado</p>
                </div>
                <nav class="site-navigation">
                    <ul>
                        <li><a href="/" class="<?= $active_page === 'home' ? 'active' : '' ?>">Inicio</a></li>
                        <li><a href="/dashboard" class="<?= $active_page === 'dashboard' ? 'active' : '' ?>">Panel de Control</a></li>
                        <li><a href="/profile" class="<?= $active_page === 'profile' ? 'active' : '' ?>">Mi Perfil</a></li>
                        <li><a href="/upload" class="<?= $active_page === 'upload' ? 'active' : '' ?>">Subir Archivo</a></li>
                        <li><a href="/gallery" class="<?= $active_page === 'gallery' ? 'active' : '' ?>">Galería</a></li>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="/admin" class="<?= $active_page === 'admin' ? 'active' : '' ?>">Administración</a></li>
                        <?php endif; ?>
                        <li><a href="/logout" class="<?= $active_page === 'logout' ? 'active' : '' ?>">Cerrar Sesión</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- Contenido Principal -->
        <div class="container">
            <?php if ($show_sidebar): ?>
                <div class="row">
                    <div class="col-main">
                        <?php echo $content; ?>
                    </div>
                    <div class="col-sidebar">
                        <!-- Barra Lateral -->
                        <aside class="sidebar">
                            <section class="widget widget-search">
                                <h3>Buscar</h3>
                                <form method="GET" action="/search">
                                    <input type="text" name="q" placeholder="Buscar...">
                                    <button type="submit">Buscar</button>
                                </form>
                            </section>

                            <section class="widget widget-recent">
                                <h3>Actividad Reciente</h3>
                                <ul>
                                    <li><a href="/">Visita al inicio</a></li>
                                    <li><a href="/dashboard">Panel de control</a></li>
                                    <li><a href="/profile">Editar perfil</a></li>
                                </ul>
                            </section>

                            <section class="widget widget-stats">
                                <h3>Estadísticas Rápidas</h3>
                                <p>��<strong>Usuarios en línea:</strong> <?= rand(5, 25) ?></p>
                                <p>��<strong>Archivos hoy:</strong> <?= rand(0, 10) ?></p>
                                <p>��<strong>Eventos de seguridad:</strong> <?= rand(0, 5) ?></p>
                            </section>
                        </aside>
                    </div>
                </div>
            <?php else: ?>
                <?php echo $content; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <?php if ($show_footer): ?>
            <footer class="site-footer">
                <div class="container">
                    <div class="footer-info">
                        <p>&copy; <?= date('Y') ?> BlackForge Labs. Todos los derechos reservados.</p>
                        <p>Instalación Omega - Entorno de Entrenamiento CTF</p>
                    </div>
                    <div class="footer-links">
                        <a href="/">Inicio</a> |
                        <a href="/dashboard">Panel</a> |
                        <a href="/login">Ingresar</a> |
                        <a href="/contact">Contacto</a>
                    </div>
                </div>
            </footer>
        <?php endif; ?>

        <!-- Scripts -->
        <script src="/assets/js/main.js"></script>
        <?php if (!empty($custom_js)): ?>
            <script src="<?= htmlspecialchars($custom_js) ?>"></script>
        <?php endif; ?>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
?>