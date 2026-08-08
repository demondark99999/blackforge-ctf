-- Datos iniciales para BlackForge Labs - Instalación Omega
-- Incluye cuentas de prueba y configuración básica

-- Insertar usuario administrador
-- Usuario: admin / Contraseña: AdminSecurePass!2024
-- (Hash MD5 intencionalmente vulnerable para entrenamiento)
INSERT INTO users (username, email, password_hash, full_name, role, is_active, created_at, last_login)
VALUES
('admin', 'admin@forgelabs.local', MD5('AdminSecurePass!2024admin'), 'Administrador del Sistema', 'admin', 1, NOW() - INTERVAL 30 DAY, NOW() - INTERVAL 1 HOUR),
('operator', 'operator@forgelabs.local', MD5('OperatorPass!2024operator'), 'Operador de Sistema', 'user', 1, NOW() - INTERVAL 25 DAY, NOW() - INTERVAL 2 HOUR),
('analyst', 'analyst@forgelabs.local', MD5('AnalystPass!2024analyst'), 'Analista de Seguridad', 'user', 1, NOW() - INTERVAL 20 DAY, NOW() - INTERVAL 3 HOUR),
('testuser', 'test@forgelabs.local', MD5('testtest'), 'Usuario de Prueba', 'user', 1, NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 5 MINUTES),
('vulnuser', 'vuln@forgelabs.local', MD5('password123vulnuser'), 'Usuario Vulnerable', 'user', 1, NOW() - INTERVAL 5 DAY, NOW() - INTERVAL 10 MINUTES);

-- Insertar configuración básica del sistema
INSERT INTO system_config (config_key, config_value, description, updated_by)
VALUES
('site_name', 'BlackForge Labs - Instalación Omega', 'Nombre del sitio mostrado en el encabezado', 1),
('site_description', 'Entorno de entrenamiento avanzado en ciberseguridad', 'Descripción del sitio', 1),
('max_upload_size', '10485760', 'Tamaño máximo de archivo en bytes (10MB)', 1),
('allowed_extensions', 'jpg,jpeg,png,gif,pdf,txt,doc,docx', 'Extensiones de archivo permitidas para upload', 1),
('session_timeout', '3600', 'Tiempo de espera de sesión en segundos', 1),
('login_attempts_limit', '5', 'Número máximo de intentos fallidos antes de bloqueo', 1),
('login_lockout_time', '900', 'Tiempo de bloqueo en segundos después de exceder intentos', 1),
('debug_mode', 'false', 'Modo de depuración (desactivado en producción)', 1),
('maintenance_mode', 'false', 'Modo de mantenimiento del sitio', 1);

-- Insertar algunos registros de log iniciales
INSERT INTO log_entries (user_id, event_type, event_description, ip_address, user_agent, created_at)
VALUES
(1, 'SYSTEM_START', 'Sistema iniciado correctamente', '127.0.0.1', 'System Boot', NOW() - INTERVAL 29 DAY),
(1, 'CONFIG_UPDATE', 'Configuración del sitio actualizada', '127.0.0.1', 'System Update', NOW() - INTERVAL 28 DAY),
(2, 'LOGIN_SUCCESS', 'Inicio de sesión exitoso', '192.168.1.100', 'Mozilla/5.0 (Training Environment)', NOW() - INTERVAL 24 DAY),
(3, 'FILE_UPLOAD', 'Archivo subido: report.pdf', '192.168.1.101', 'Mozilla/5.0 (Training Environment)', NOW() - INTERVAL 20 DAY),
(4, 'LOGIN_FAILED', 'Intento de inicio de sesión fallido', '10.0.0.5', 'curl/7.68.0', NOW() - INTERVAL 1 HOUR),
(5, 'LOGIN_FAILED', 'Intento de inicio de sesión fallido', '10.0.0.5', 'curl/7.68.0', NOW() - INTERVAL 55 MINUTES),
(5, 'LOGIN_FAILED', 'Intento de inicio de sesión fallido', '10.0.0.5', 'curl/7.68.0', NOW() - INTERVAL 50 MINUTES),
(5, 'LOGIN_SUCCESS', 'Inicio de sesión exitoso tras fuerza bruta', '10.0.0.5', 'curl/7.68.0', NOW() - INTERVAL 45 MINUTES);

-- Insertar algunos archivos de ejemplo
INSERT INTO uploads (user_id, filename, original_name, mime_type, file_size, upload_path, is_public, scanned)
VALUES
(1, 'manual_2024.pdf', 'Manual del Usuario.pdf', 'application/pdf', 2457600, '/uploads/manual_2024.pdf', 1, 1),
(2, 'network_diagram.png', 'Diagrama de Red.png', 'image/png', 1024000, '/uploads/network_diagram.png', 1, 1),
(3, 'security_audit.docx', 'Informe de Auditoría de Seguridad.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 512000, '/uploads/security_audit.docx', 0, 1),
(4, 'test_file.txt', 'Archivo de Prueba.txt', 'text/plain', 1024, '/uploads/test_file.txt', 1, 1);

-- Insertar algunos comentarios de ejemplo
INSERT INTO comments (user_id, content, created_at, is_approved)
VALUES
(1, 'Bienvenidos al entorno de entrenamiento BlackForge Labs. Este sistema contiene vulnerabilidades intencionales para practicar técnicas de explotación y defensa.', NOW() - INTERVAL 25 DAY, 1),
(2, 'Recuerde que todas las actividades están siendo registradas. El uso no autorizado de este sistema puede tener consecuencias legales.', NOW() - INTERVAL 20 DAY, 1),
(3, '¿Alguien ha encontrado la forma de escalar privilegios desde la cuenta de testuser?', NOW() - INTERVAL 15 DAY, 1),
(4, 'He subido un archivo con contenido interesante en la sección de uploads. revisen los permisos.', NOW() - INTERVAL 10 DAY, 1),
(5, 'Necesito ayuda para acceder al panel de administración. ¿Alguna pista?', NOW() - INTERVAL 5 DAY, 1);