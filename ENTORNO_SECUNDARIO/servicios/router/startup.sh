#!/bin/bash
# Script de inicio para el router FRR en contenedor Docker
# BlackForge Labs - Instalación Omega - Entorno Secundario

set -e

echo "Iniciando servicios FRR..."

# Asegurar que los directorios de estado existen
mkdir -p /var/run/frr
chown -R frr:frr /var/run/frr

# Iniciar los daemon de FRR
/usr/lib/frr/zebra -d -A 127.0.0.1
/usr/lib/frr/bgpd -d -A 127.0.0.1
/usr/lib/frr/ospfd -d -A 127.0.0.1
/usr/lib/frr/ripd -d -A 127.0.0.1
/usr/lib/frr/staticd -d -A 127.0.0.1

# Esperar un momento para que los servicios se inicien
sleep 2

# Verificar que los servicios estén corriendo
echo "Verificando servicios FRR..."
ps aux | grep frr

# Mostrar la configuración de vtysh para acceso
echo ""
echo "Para acceder a la CLI de FRR, use:"
echo "telnet localhost 2601"
echo "o"
echo "nc localhost 2601"
echo ""

# Mantener el contenedor en ejecución
while true; do
    sleep 60
    # Verificar que los procesos aún estén corriendo
    if ! pgrep -x "zebra" > /dev/null; then
        echo "zebra se detuvo, reiniciando..."
        /usr/lib/frr/zebra -d -A 127.0.0.1 &
    fi
    if ! pgrep -x "bgpd" > /dev/null; then
        echo "bgpd se detuvo, reiniciando..."
        /usr/lib/frr/bgpd -d -A 127.0.0.1 &
    fi
    if ! pgrep -x "ospfd" > /dev/null; then
        echo "ospfd se detuvo, reiniciando..."
        /usr/lib/frr/ospfd -d -A 127.0.0.1 &
    fi
    if ! pgrep -x "ripd" > /dev/null; then
        echo "ripd se detuvo, reiniciando..."
        /usr/lib/frr/ripd -d -A 127.0.0.1 &
    fi
done