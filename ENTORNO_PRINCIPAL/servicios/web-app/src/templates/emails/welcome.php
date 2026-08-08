<?php
/**
 * Plantilla de Correo de Bienvenida
 * BlackForge Labs - Instalación Omega
 *
 * Plantilla para correos electrónicos automatizados
 * Contiene vulnerabilidades intencionales para entrenamiento CTF
 */

/**
 * Renderiza la plantilla de correo de bienvenida
 * @param array $datos Información para personalizar el correo
 * @return string HTML del correo
 */
function renderWelcomeEmail(array $datos = []): string {
    // Extraer datos con valores predeterminados
    $username = htmlspecialchars($datos['username'] ?? 'Usuario');
    $app_name = htmlspecialchars($datos['app_name'] ?? 'BlackForge Labs');
    $support_email = htmlspecialchars($datos['support_email'] ?? 'support@forgelabs.local');
    $login_url = htmlspecialchars($datos['login_url'] ?? 'http://localhost/login');
    $year = date('Y');

    // Vulnerabilidad intencional: falta de validación de URLs que podría levar a XSS
    // En una aplicación real, se validarían las URLs para prevenir inyección

    // Vulnerabilidad intencional: uso de datos directamente sin escape adecuado en algunos contextos
    // Aunque usamos htmlspecialchars arriba, en una plantilla real podría haber contexto donde se necesite diferente escaping

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenido a <?= $app_name ?></title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c3e50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 5px 5px; }
            .button { display: inline-block; background: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; font-size: 0.9em; color: #666; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Bienvenido a <?= $app_name ?></h1>
            <p>Su cuenta ha sido creada exitosamente</p>
        </div>
        <div class="content">
            <p>Hola <?= $username ?>,</p>
            <p>Gracias por unirse a <?= $app_name ?>, su entorno de entrenamiento avanzado en ciberseguridad.</p>
            <p>Para comenzar, simplemente haga clic en el botón de abajo para iniciar sesión:</p>
            <a href="<?= $login_url ?>" class="button">Iniciar Sesión</a>

            <h2>Detalles de su cuenta:</h2>
            <ul>
                <li>Usuario: <?= $username ?></li>
                <li>Correo de registro: <?= isset($datos['email']) ? htmlspecialchars($datos['email']) : 'No proporcionado' ?></li>
                <li>Fecha de registro: <?= date('d/m/Y H:i:s') ?></li>
            </ul>

            <p>Si tiene alguna pregunta o necesita asistencia, no dude en contactarnos en:</p>
            <p><a href="mailto:<?= $support_email ?>"><?= $support_email ?></a></p>

            <hr>
            <p><small>Este es un correo electrónico automatizado. Por favor no responda a este mensaje.</small></p>
        </div>
        <div class="footer">
            <p>&copy; <?= $year ?> <?= $app_name ?>. Todos los derechos reservados.</p>
            <p>Instalación Omega - Entorno de Entrenamiento CTF</p>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
?>