#!/bin/bash
# Script de firewall para BlackForge Labs - Instalación Omega
# Carga las reglas de iptables para el entorno de entrenamiento CTF

set -e

echo "Iniciando configuración de firewall para BlackForge Labs..."

# Flush cualquier regla existente
iptables -F
iptables -X
iptables -t nat -F
iptables -t nat -X
iptables -t mangle -F
iptables -t mangle -X
iptables -t raw -F
iptables -t raw -X

# Establecer políticas predeterminadas
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT
iptables -t nat -P PREROUTING ACCEPT
iptables -t nat -P POSTROUTING ACCEPT

# Cargar reglas desde el archivo si existe
if [ -f /etc/iptables.rules ]; then
    echo "Cargando reglas desde /etc/iptables.rules..."
    iptables-restore < /etc/iptables.rules
else
    echo "Advertencia: Archivo de reglas no encontrado en /etc/iptables.rules"
    echo "Aplicando reglas básicas..."

    # Reglas básicas como fallback
    iptables -A INPUT -i lo -j ACCEPT
    iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
    iptables -A INPUT -p tcp --dport 22 -j ACCEPT
    iptables -A INPUT -p tcp --dport 80 -j ACCEPT
    iptables -A INPUT -p tcp --dport 443 -j ACCEPT
    iptables -A INPUT -p tcp --dport 21 -j ACCEPT
    iptables -A INPUT -p tcp --dport 20 -j ACCEPT
    iptables -A INPUT -p tcp --match multiport --dports 21100:21110 -j ACCEPT
    iptables -A INPUT -p tcp --dport 25 -j ACCEPT
    iptables -A INPUT -p tcp --dport 587 -j ACCEPT
    iptables -A INPUT -p tcp --dport 3306 -j ACCEPT
    iptables -A INPUT -p tcp --dport 6379 -j ACCEPT
fi

# Mostrar reglas cargadas
echo "Reglas de firewall cargadas:"
iptables -L -v -n
echo "----------------------------------------"
iptables -t nat -L -v -n

# Mantener el contenedor en ejecución
echo "Firewall inicializado. Manteniendo contenedor en ejecución..."
while true; do
    sleep 60
    # Opcional: recargar reglas periódicamente
    # iptables-restore < /etc/iptables.rules
done