#!/bin/bash
# Script de inicio para Snort IDS en contenedor Docker
# BlackForge Labs - Instalación Omega - Entorno Secundario

set -e

echo "Iniciando servicio Snort IDS..."

# Asegurar que el directorio de logs existe
mkdir -p /var/log/snort
chown -R snort:snort /var/log/snort 2>/dev/null || chown -R nobody:nogroup /var/log/snort || true

# Esperar a que las interfaces de red estén listas
sleep 5

# Iniciar Snort en modo daemon
echo "Iniciando Snort en modo de detección..."
snort -i eth0 -c /etc/snort/snort.conf -l /var/log/snort -D

# Esperar a que el servicio se inicie
sleep 3

# Verificar que el servicio esté corriendo
if pgrep snort > /dev/null; then
    echo "Snort IDS iniciado exitosamente"
else
    echo "Error: Snort no se inició correctamente"
    # Intentar iniciar en modo foreground para ver el error
    echo "Intentando inicio en modo foreground para diagnóstico..."
    snort -i eth0 -c /etc/snort/snort.conf -l /var/log/snort -v
    exit 1
fi

# Mostrar información de servicio
echo ""
echo "Snort IDS está activo y monitoreando tráfico en interfaz eth0"
echo "Los logs se escriben en: /var/log/snort/"
echo ""

# Mantener el contenedor en ejecución y verificar el servicio
while true; do
    sleep 30
    if ! pgrep snort > /dev/null; then
        echo "Snort se detuvo, reiniciando..."
        snort -i eth0 -c /etc/snort/snort.conf -l /var/log/snort -D
        sleep 3
    fi
done