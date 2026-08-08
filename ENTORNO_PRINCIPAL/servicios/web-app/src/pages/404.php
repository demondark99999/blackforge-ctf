<?php
/**
 * Página de Error 404
 * BlackForge Labs - Instalación Omega
 */

require_once __DIR__ . '/../../config/security.php';

$page_title = 'Página No Encontrada - BlackForge Labs';
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
        <div class="error-container">
            <div class="error-code">404</div>
            <h1>Página No Encontrada</h1>
            <p>Lo sentimos, la página que está buscando no existe o ha sido移动.</p>
            <div class="error-actions">
                <a href="/" class="btn btn-primary">Volver al Inicio</a>
                <a href="/login" class="btn btn-secondary">Iniciar Sesión</a>
            </div>
            <div class="error-hints">
                <h3>¿Qué puede hacer?</h3>
                <ul>
                    <li>Verifique que la URL esté escrita correctamente</li>
                    <li>Regrese a la página anterior y intente un enlace diferente</li>
                    <li>Utilice el buscador si está disponible</li>
                    <li>Contacte al administrador si el problema persiste</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>