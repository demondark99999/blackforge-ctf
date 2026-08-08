#!/bin/bash
# Script de respaldo para el entorno principal de BlackForge Labs
# Instalación Omega - Entorno de entrenamiento CTF

set -e

echo "Iniciando proceso de respaldo de BlackForge Labs..."
echo "=================================================="

# Configuración
BACKUP_DIR="/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_NAME="forge_backup_${TIMESTAMP}"
DB_BACKUP="$BACKUP_DIR/${BACKUP_NAME}_db.sql"
UPLOADS_BACKUP="$BACKUP_DIR/${BACKUP_NAME}_uploads.tar.gz"
LOGS_BACKUP="$BACKUP_DIR/${BACKUP_NAME}_logs.tar.gz"
CONFIG_BACKUP="$BACKUP_DIR/${BACKUP_NAME}_config.tar.gz"

# Crear directorio de respaldos si no existe
mkdir -p "$BACKUP_DIR"

echo "Directorio de respaldos: $BACKUP_DIR"
echo "Timestamp: $TIMESTAMP"
echo ""

# Respaldar base de datos
echo "Respaldando base de datos..."
if docker exec forge_db mysqldump -u Forge_user -p"$DB_PASSWORD" forge_db > "$DB_BACKUP"; then
    echo "��✓ Base de datos respaldada exitosamente: $DB_BACKUP"
else
    echo "��✗ Error al respaldar la base de datos"
    exit 1
fi

# Respaldar uploads
echo "Respaldando archivos subidos..."
if docker exec forge_web-app tar -czf - /var/www/html/uploads > "$UPLOADS_BACKUP" 2>/dev/null; then
    echo "��✓ Uploads respaldados exitosamente: $UPLOADS_BACKUP"
else
    echo "��✗ Error al respaldar uploads"
    # No salir porque podría no haber uploads
fi

# Respaldar logs
echo "Respaldando logs..."
if docker exec forge_web-app tar -czf - /var/log/www > "$LOGS_BACKUP" 2>/dev/null; then
    echo "��✓ Logs respaldados exitosamente: $LOGS_BACKUP"
else
    echo "��✗ Error al respaldar logs"
fi

# Respaldar configuración (si aplica)
echo "Respaldando configuración..."
if docker exec forge_web-app tar -czf - /var/www/html/config > "$CONFIG_BACKUP" 2>/dev/null; then
    echo "��✓ Configuración respaldada exitosamente: $CONFIG_BACKUP"
else
    echo "��✗ Error al respaldar configuración"
fi

# Verificar integridad de los respaldos
echo ""
echo "Verificando integridad de respaldos..."
if [ -s "$DB_BACKUP" ]; then
    echo "��✓ Respaldo de base de datos válido"
else
    echo "��✗ Respaldo de base de datos vacío o corrupto"
fi

# Mostrar resumen
echo ""
echo "Respaldo completado exitosamente!"
echo "=================================================="
echo "Respaldo creado: $BACKUP_NAME"
echo "Tamaño del respaldo de DB: $(du -h "$DB_BACKUP" | cut -f1)"
echo "Ubicación: $BACKUP_DIR"
echo ""
echo "Para restaurar este respaldo, use el script restore.sh"
echo "Los respaldos se mantendrán por 30 días por defecto."