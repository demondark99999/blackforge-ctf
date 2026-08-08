#!/bin/bash
# Script de inicialización para el entorno principal de BlackForge Labs
# Instalación Omega - Entorno de entrenamiento CTF

set -e

echo "Inicializando entorno principal de BlackForge Labs..."
echo "=================================================="

# Esperar a que los servicios estén disponibles
echo "Esperando a que los servicios esenciales estén disponibles..."

# Esperar a la base de datos
echo -n "Esperando a la base de datos..."
until nc -z database 3306; do
    echo -n "."
    sleep 1
done
echo " ¡Listo!"

# Esperar a Redis
echo -n "Esperando a Redis..."
until nc -z cache 6379; do
    echo -n "."
    sleep 1
done
echo " ¡Listo!"

# Ejecutar migraciones de base de datos si es necesario
echo "Verificando estado de la base de datos..."
# En una aplicación real, aquí ejecutaríamos migraciones
# Para este entorno, los datos se inicializan mediante los scripts de Docker

# Crear directorios necesarios si no existen
echo "Creando directorios necesarios..."
mkdir -p /var/www/html/uploads
mkdir -p /var/log/www
mkdir -p /var/log/nginx
chown -R www-data:www-data /var/www/html/uploads
chown -R www-data:www-data /var/log/www

# Establecer permisos apropiados
echo "Estableciendo permisos..."
chmod -R 755 /var/www/html
chmod -R 750 /var/www/html/uploads  # Los uploads deberían ser escribibles pero no ejecutables idealmente

# Configurar variables de entorno si no están establecidas
if [ -z "$APP_TIMEZONE" ]; then
    export APP_TIMEZONE="UTC"
fi

echo "Inicialización completada."
echo "=================================================="
echo "El entorno está listo para recibir conexiones."
echo "Puede acceder a la aplicación en: http://localhost"
echo "Para административный доступ, use las credenciales: admin / AdminSecurePass!2024"