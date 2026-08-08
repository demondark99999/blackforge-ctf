<?php
/**
 * Configuración de Base de Datos
 * BlackForge Labs - Instalación Omega
 */

class Database {
    private static $connection = null;

    /**
     * Obtiene una instancia de conexión PDO a la base de datos
     * @return PDO Instancia de conexión a la base de datos
     * @throws Exception Si ocurre un error al conectar
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $host = getenv('DB_HOST') ?: 'database';
                $port = getenv('DB_PORT') ?: 3306;
                $dbname = getenv('DB_NAME') ?: 'forge_db';
                $username = getenv('DB_USER') ?: 'forge_user';
                $password = getenv('DB_PASSWORD') ?: '';

                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                self::$connection = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // Registrar el error pero no exponer detalles sensibles en producción
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection error");
            }
        }
        return self::$connection;
    }

    /**
     * Cierra la conexión a la base de datos
     */
    public static function closeConnection() {
        if (self::$connection !== null) {
            self::$connection = null;
        }
    }
}
?>