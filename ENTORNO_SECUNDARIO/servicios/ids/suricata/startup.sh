#!/bin/bash
# Script de inicio para Suricata IDS/IPS en contenedor Docker
# BlackForge Labs - Instalación Omega - Entorno Secundario

set -e

echo "Iniciando servicio Suricata IDS/IPS..."

# Asegurar que el directorio de logs existe
mkdir -p /var/log/suricata
chown -R nobody:nogroup /var/log/suricata 2>/dev/null || true

# Esperar a que las interfaces de red estén listas
sleep 5

# Iniciar Suricata en modo IPS (prevención) con interfaz en modo promiscuo
echo "Iniciando Suricata en modo de prevención (IPS)..."
suricata -c /etc/suricata/suricata.yaml -i eth0 --init-errors-fatal -D

# Esperar a que el servicio se inicie
sleep 5

# Verificar que el servicio esté corriendo
if pgrep suricata > /dev/null; then
    echo "Suricata IDS/IPS iniciado exitosamente"
else
    echo "Error: Suricata no se inició correctamente"
    # Intentar iniciar en modo foreground para ver el error
    echo "Intentando inicio en modo foreground para diagnóstico..."
    suricata -c /etc/suricata/suricata.yaml -i eth0 --init-errors-fatal -v
    exit 1
fi

# Mostrar información de servicio
echo ""
echo "Suricata IDS/IPS está activo y trabajando en modo de prevención"
print "Está inspectando y potencialmente bloqueando tráfico malicioso en interfaz eth0"
echo "Los logs se escriben en: /var/log/suricata/"
echo ""

# Mantener el contenedor en ejecución y verificar el servicio
while true; do
    sleep 30
    if ! pgrep suricata > /dev/null; then
        echo "Suricata se detuvo, reiniciando..."
        suricata -c /etc/suricata/suricata.yaml -i eth0 --init-errors-fatal -D
        sleep 5
    fi
done