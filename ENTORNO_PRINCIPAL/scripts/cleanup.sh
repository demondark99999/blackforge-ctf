#!/bin/bash
# Script de limpieza para el entorno principal de BlackForge Labs
# Instalación Omega - Entorno de entrenamiento CTF

set -e

echo "Iniciando proceso de limpieza de BlackForge Labs..."
echo "=================================================="

# Limpiar contenedores detenidos y imágenes dangling
echo "Limpiando recursos Docker no utilizados..."
docker system prune -f --volumes

# Limpiar logs antiguos (más de 7 días)
echo "Limpiando logs antiguos..."
find /var/log/nginx -name "*.log" -mtime +7 -delete 2>/dev/null || true
find /var/log/www -name "*.log" -mtime +7 -delete 2>/dev/null || true
find /var/log/mail -name "*.log" -mtime +7 -delete 2>/dev/null || true
find /var/log/vsftpd -name "*.log" -mtime +7 -delete 2>/dev/null || true
find /var/log/monitoring -name "*.log" -mtime +7 -delete 2>/dev/null || true

# Limpiar archivos temporales
echo "Limpiando archivos temporales..."
find /tmp -type f -atime +1 -delete 2>/dev/null || true
find /var/tmp -type f -atime +1 -delete 2>/dev/null || true

# Limpiar respaldos antiguos (más de 30 días)
echo "Limpiando respaldos antiguos (>30 días)..."
find /backups -name "forge_backup_*" -mtime +30 -delete 2>/dev/null || true

# Mostrar uso de disco después de la limpieza
echo ""
echo "Uso de disco después de la limpieza:"
df -h | grep -E '(Filesystem|/dev/)' | head -5
echo ""
echo "Espacio utilizado por Docker:"
docker system df

echo ""
echo "Limpieza completada exitosamente!"
echo "=================================================="