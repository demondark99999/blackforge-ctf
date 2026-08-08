#!/bin/bash
# Script de despliegue para el entorno principal de BlackForge Labs
# Instalación Omega - Entorno de entrenamiento CTF

set -e

echo "Desplegando entorno principal de BlackForge Labs..."
echo "=================================================="

# Construir todas las imágenes
echo "Construyendo imágenes Docker..."
docker-compose build

# Iniciar todos los servicios
echo "Iniciando servicios..."
docker-compose up -d

# Esperar a que los servicios estén saludables
echo "Esperando a que los servicios estén saludables..."
MAX_ATTEMPTS=30
ATTEMPT=0

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    echo -n "Intento $((ATTEMPT+1))/$MAX_ATTEMPTS: "

    # Verificar healthcheck de nginx-proxy
    if curl -s -f http://localhost/healthz > /dev/null; then
        echo "¡Servicios saludables!"
        break
    fi

    echo "Esperando..."
    ATTEMPT=$((ATTEMPT+1))
    sleep 10
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "Error: Tiempo de espera agotado esperando a que los servicios estén saludables"
    echo "Verifique los logs con: docker-compose logs"
    exit 1
fi

# Mostrar información de los servicios
echo "Información de servicios:"
docker-compose ps

echo ""
echo "Despliegue completado exitosamente!"
echo ""
echo "URLs de acceso:"
echo "  - Aplicación web: http://localhost"
echo "  - Panel de administración: http://localhost/admin"
echo ""
echo "Credenciales de prueba:"
echo "  - Usuario: admin"
echo "  - Contraseña: AdminSecurePass!2024"
echo ""
echo "Para ver los logs:"
echo "  docker-compose logs -f"
echo ""
echo "Para detener el entorno:"
echo "  docker-compose down"