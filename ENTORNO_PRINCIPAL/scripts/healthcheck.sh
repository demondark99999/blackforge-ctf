#!/bin/bash
# Script de verificación de salud para todos los servicios
# BlackForge Labs - Instalación Omega
# Entorno de entrenamiento CTF

set -e

echo "Verificando salud de todos los servicios en BlackForge Labs..."
echo "=================================================="

# Configuración
TIMEOUT=5
FAILED=0
PASSED=0

# Definir servicios a verificar
declare -A SERVICES=(
    ["nginx-proxy"]="http://localhost/healthz"
    ["web-app"]="http://localhost:8080/healthz"
    ["database"]="tcp://database:3306"
    ["cache"]="tcp://cache:6379"
    ["mail"]="tcp://localhost:25"
    ["ftp-server"]="tcp://localhost:21"
    ["monitoring"]="tcp://localhost:9100"
)

# Función para verificar un servicio HTTP
check_http() {
    local name=$1
    local url=$2

    if curl -s -f --max-time $TIMEOUT "$url" > /dev/null; then
        echo "��✓ $name: OK"
        ((PASSED++))
        return 0
    else
        echo "��✗ $name: FALLÓ (HTTP)"
        ((FAILED++))
        return 1
    fi
}

# Función para verificar un servicio TCP
check_tcp() {
    local name=$1
    local host_port=$2

    # Extraer host y puerto
    local host=${host_port#tcp://}
    local port=${host##*:}
    local host_only=${host%:*}

    if nc -z -w $TIMEOUT $host_only $port > /dev/null 2>&1; then
        echo "��✓ $name: OK"
        ((PASSED++))
        return 0
    else
        echo "��✗ $name: FALLÓ (TCP)"
        ((FAILED++))
        return 1
    fi
}

# Verificar cada servicio
for service in "${!SERVICES[@]}"; do
    url="${SERVICES[$service]}"
    if [[ $url == http* ]]; then
        check_http "$service" "$url"
    else
        check_tcp "$service" "$url"
    fi
done

echo ""
echo "Resumen de verificación:"
echo "  Servicios verificados: $((PASSED + FAILED))"
echo "  Servicios correctos: $PASSED"
echo "  Servicios fallidos: $FAILED"

if [ $FAILED -eq 0 ]; then
    echo ""
    echo "�������������� Todos los servicios están saludables!"
    exit 0
else
    echo ""
    echo "��������������  Algunos servicios presentan problemas."
    echo "   Revise los logs con: docker-compose logs <servicio>"
    exit 1
fi