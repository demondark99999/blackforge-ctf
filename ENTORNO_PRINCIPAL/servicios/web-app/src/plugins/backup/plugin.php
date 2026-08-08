<?php
/**
 * Plugin de Respaldos Básico
 * BlackForge Labs - Instalación Omega
 *
 * Plugin de ejemplo para demostración del sistema de plugins
 * Contiene vulnerabilidades intencionales para entrenamiento CTF
 */

class BackupPlugin {
    private $name = 'Backup Basic';
    private $version = '1.0.0';
    private $description = 'Plugin básico para creación de respaldos del sistema';
    private $author = 'BlackForge Labs';
    private $active = false;

    public function __construct() {
        // Vulnerabilidad intencional: falta de sanitización en parámetros de construcción
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
        // Vulnerabilidad intencional: creación de tareas programadas sin validación adecuada
        $this->active = true;
        error_log("Plugin de respaldos activado en " . date('Y-m-d H:i:s'));
        return true;
    }

    public function deactivate() {
        $this->active = false;
        error_log("Plugin de respaldos desactivado en " . date('Y-m-d H:i:s'));
        return true;
    }

    public function executeHook($hook_name, $params = []) {
        switch ($hook_name) {
            case 'before_backup':
                // Vulnerabilidad intencional: ejecución de comandos sin validación adecuada
                if (isset($params['type']) && $params['type'] === 'full') {
                    // En un sistema real, se validaría y sanitizaría el tipo de respaldo
                    // Aquí simulamos una vulnerabilidad de inyección de comando
                    $backup_command = "mysqldump -u root -p'${DB_PASSWORD}' --all-databases > /backup/full_" . time() . ".sql";
                    // Vulnerabilidad intencional: el comando no se ejecuta realmente pero se registra
                    error_log("Simulando comando de respaldo: " . $backup_command);
                }
                break;

            case 'after_file_upload':
                // Vulnerabilidad intencional: escaneo insuficiente de archivos subidos
                if (isset($params['file_path'])) {
                    // En un sistema real, se escanearía el archivo en busca de malware
                    // Aquí simplemente registramos sin hacer nada
                    error_log("Archivo subido: " . $params['file_path'] . " - Escaneo de seguridad omitido (intencional)");
                }
                break;
        }

        return $params;
    }

    public function getSettingsForm() {
        // Vulnerabilidad intencional: formulario sin protección CSRF
        return '
            <h3>Configuración del Plugin de Respaldos</h3>
            <form method="POST" action="/admin/plugins/backup/config">
                <div class="form-group">
                    <label for="backup_frequency">Frecuencia de Respaldos:</label>
                    <select id="backup_frequency" name="backup_frequency" class="form-control">
                        <option value="hourly">Cada hora</option>
                        <option value="daily">Diario</option>
                        <option value="weekly">Semanal</option>
                        <option value="monthly">Mensual</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="backup_retention">Retención de Respaldos:</label>
                    <input type="number" id="backup_retention" name="backup_retention" value="30" min="1" class="form-control">
                    <small>Días para mantener respaldos</small>
                </div>
                <div class="form-group">
                    <label for="backup_location">Ubicación de Respaldos:</label>
                    <input type="text" id="backup_location" name="backup_location" value="/backups" class="form-control">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                </div>
            </form>
        ';
    }

    public function saveSettings($data) {
        // Vulnerabilidad intencional: falta de validación de entradas
        error_log("Guardando configuración de respaldos: " . print_r($data, true));
        return true;
    }

    // Método vulnerable intencionalmente: permite ejecución de comando arbitrario
    public function executeBackupCommand($command) {
        // �������� ������ ������ ���� ADVERTENCIA: Esta es una vulnerabilidad intencional de inyección de comando
        // NO hacer esto en una aplicación real
        // En un entorno de entrenamiento, esto podría permitir ejecución arbitraria de comandos
        error_log("Ejecutando comando de respaldo: " . $command);
        // En una versión real "peligrosa", aquí se ejecutaría: shell_exec($command);
        // Pero por seguridad en este entorno, solo lo registramos
        return [
            'success' => true,
            'message' => 'Comando registrado (no ejecutado por seguridad en este entorno)',
            'command' => $command
        ];
    }
}

// Instanciar y registrar el plugin
$backupPlugin = new BackupPlugin();
?>