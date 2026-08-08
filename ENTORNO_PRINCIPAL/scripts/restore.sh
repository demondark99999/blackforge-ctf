#!/bin/bash
# Script de restauración para el entorno principal de BlackForge Labs
# Instalación Omega - Entorno de entrenamiento CTF

set -e

echo "Restaurando entorno de BlackForge Labs desde respaldo..."
echo "=================================================="

# Verificar argumentos
if [ $# -lt 1 ]; then
    echo "Uso: $0 <timestamp_respaldo>"
    echo "Ejemplo: $0 20240115_143022"
    echo ""
    echo "Respaldos disponibles:"
    ls -la /backups/forge_backup_* 2>/dev/null || echo "No se encontraron respaldos"
    exit 1
fi

TIMESTAMP=$1
BACKUP_DIR="/backups"
DB_BACKUP="$BACKUP_DIR/forge_backup_${TIMESTAMP}_db.sql"
UPLOADS_BACKUP="$BACKUP_DIR/forge_backup_${TIMESTAMP}_uploads.tar.gz"
LOGS_BACKUP="$BACKUP_DIR/forge_backup_${TIMESTAMP}_logs.tar.gz"
CONFIG_BACKUP="$BACKUP_DIR/forge_backup_${TIMESTAMP}_config.tar.gz"

# Verificar que el respaldo existe
if [ ! -f "$DB_BACKUP" ]; then
    echo "Error: No se encontró el respaldo de base de datos para el timestamp $TIMESTAMP"
    echo "Archivo esperado: $DB_BACKUP"
    exit 1
fi

echo "Se restaurará desde el respaldo: forge_backup_${TIMESTAMP}"
echo ""
echo "��⚠��️  ADVERTENCIA: Este proceso sobrescribirá los datos actuales."
echo "   Se recomienda crear un respaldo de seguridad antes de continuar."
read -p "¿Está seguro de que quiere continuar? (y/N): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Operación cancelada por el usuario."
    exit 1
fi

# Detener servicios que puedan interferir con la restauración
echo "Deteniendo servicios críticos..."
docker-compose stop web-app || true
docker-compose stop ftp-server || true

# Restaurar base de datos
echo "Restaurando base de datos..."
if docker exec -i forge_db mysql -u Forge_user -p"$DB_PASSWORD" forge_db < "$DB_BACKUP"; then
    echo "������✓ Base de datos restaurada exitosamente"
else
    echo "������✗ Error al restaurar la base de datos"
    # Continuar con otros componentes incluso si falla la DB
fi

# Restaurar uploads
if [ -f "$UPLOADS_BACKUP" ]; then
    echo "Restaurando uploads..."
    if docker exec forge_web-app mkdir -p /var/www/html/uploads && \
       docker exec -i forge_web-app tar -xzf - -C /var/www/html/uploads < "$UPLOADS_BACKUP"; then
        echo "������✓ Uploads restaurados exitosamente"
    else
        echo "������✗ Error al restaurar uploads"
    fi
else
    echo "���� Información: No se encontró respaldo de uploads para restaurar"
fi

# Restaurar logs
if [ -f "$LOGS_BACKUP" ]; then
    echo "Restaurando logs..."
    if docker exec forge_web-app mkdir -p /var/log/www && \
       docker exec -i forge_web-app tar -xzf - -C /var/log/www < "$LOGS_BACKUP"; then
        echo "������✓ Logs restaurados exitosamente"
    else
        echo "������✗ Error al restaurar logs"
    fi
else
    echo "���� Información: No se encontró respaldo de logs para restaurar"
fi

# Restaurar configuración
if [ -f "$CONFIG_BACKUP" ]; then
    echo "Restaurando configuración..."
    if docker exec forge_web-app mkdir -p /var/www/html/config && \
       docker exec -i forge_web-app tar -xzf - -C /var/www/html/config < "$CONFIG_BACKUP"; then
        echo "������✓ Configuración restaurada exitosamente"
    else
        echo "������✗ Error al restaurar configuración"
    fi
else
    echo "���� Información: No se encontró respaldo de configuración para restaurar"
fi

# Reiniciar servicios
echo "Reiniciando servicios..."
docker-compose start web-app
docker-compose start ftp-server

# Verificar que los servicios estén funcionando
echo "Verificando estado de los servicios..."
sleep 5
if curl -s -f http://localhost/healthz > /dev/null; then
    echo "������✓ Servicios respondiendo correctamente"
else
    echo "������⚠��️  Los servicios pueden estar tardando en iniciarse completamente"
    echo "   Verifique los logs con: docker-compose logs"
fi

echo ""
echo "Restauración completada!"
echo "=================================================="
echo "Se han restaurado los datos desde el timestamp: $TIMESTAMP"
echo ""
echo "Próximos pasos recomendados:"
echo "1. Verificar que la aplicación funcione correctamente"
echo "2. Probar el inicio de sesión con credenciales conocidas"
echo "3. Revisar los logs para detectar cualquier anomalía"