<?php
/**
 * Plugin SEO Basic
 * BlackForge Labs - Instalación Omega
 *
 * Plugin de ejemplo para demostración del sistema de plugins
 * Contiene vulnerabilidades intencionales para entrenamiento CTF
 */

class SeoPlugin {
    private $name = 'SEO Basic';
    private $version = '1.0.0';
    private $description = 'Plugin básico para optimización de motores de búsqueda';
    private $author = 'BlackForge Labs';
    private $active = false;

    public function __construct() {
        // Vulnerabilidad intencional: falta de sanitización en parámetros de construcción
        // En un sistema real, se validarían y sanitizarían las entradas
    }

    public function getInfo() {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'description' => $this->description,
            'author' => $this->author,
            'active' => $this->active
        ];
    }

    public function activate() {
        // Vulnerabilidad intencional: ejecución de código potencialmente peligrosa durante activación
        // Por ejemplo, podría modificar archivos del sistema o base de datos sin validación adecuada
        $this->active = true;

        // Registrar activación (vulnerabilidad intencional: posible inyección de log)
        error_log("Plugin SEO activado en " . date('Y-m-d H:i:s'));

        return true;
    }

    public function deactivate() {
        $this->active = false;
        error_log("Plugin SEO desactivado en " . date('Y-m-d '));
        return true;
    }

    public function executeHook($hook_name, $params = []) {
        switch ($hook_name) {
            case 'before_content_output':
                // Vulnerabilidad intencional: modificación de contenido sin escape adecuado
                if (isset($params['content'])) {
                    // Añadir мета-теги SEO básicos (simplificado para demostración)
                    $seo_tags = "\n<!-- SEO Meta Tags by SeoPlugin -->\n";
                    $seo_tags .= '<meta name="description" content="Sitio entrenado en BlackForge Labs - Contenido optimizado para SEO">' . "\n";
                    $seo_tags .= '<meta name="keywords" content="entrenamiento, ciberseguridad, CTF, blackforge, labs">' . "\n";

                    // Vulnerabilidad intencional: concatenación directa sin sanitización
                    return $seo_tags . $params['content'];
                }
                break;

            case 'after_login':
                // Vulnerabilidad intencional: redirección no validada
                if (isset($params['redirect_url']) && !empty($params['redirect_url'])) {
                    // En un sistema real, se validaría que la URL sea segura y del mismo dominio
                    header('Location: ' . $params['redirect_url']);
                    exit;
                }
                break;
        }

        return $params;
    }

    public function getSettingsForm() {
        // Vulnerabilidad intencional: exposición de formulario sin protección CSRF adecuada
        return '
            <h3>Configuración del Plugin SEO</h3>
            <form method="POST" action="/admin/plugins/seo/config">
                <div class="form-group">
                    <label for="seo_title_format">Formato de Título:</label>
                    <input type="text" id="seo_title_format" name="seo_title_format"
                           value="[TITLE] | BlackForge Labs" class="form-control">
                </div>
                <div class="form-group">
                    <label for="meta_robots">Meta Robots:</label>
                    <select id="meta_robots" name="meta_robots" class="form-control">
                        <option value="index,follow">Indexar, Seguir</option>
                        <option value="noindex,follow">No Indexar, Seguir</option>
                        <option value="index,nofollow">Indexar, No Seguir</option>
                        <option value="noindex,nofollow">No Indexar, No Seguir</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                </div>
            </form>
        ';
    }

    public function saveSettings($data) {
        // Vulnerabilidad intencional: falta de validación y sanitización de entradas
        // En un sistema real, se validarían y sanitizarían todas las entradas
        error_log("Guardando configuración SEO: " . print_r($data, true));
        return true;
    }
}

// Instanciar y registrar el plugin (en un sistema real, esto sería manejado por un gestor de plugins)
$seoPlugin = new SeoPlugin();
// El registro real sería hecho por el sistema de plugins
?>