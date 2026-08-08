#!/bin/bash
# Script de inicio para OpenVPN en contenedor Docker
# BlackForge Labs - Instalación Omega - Entorno Secundario

set -e

echo "Iniciando servicio OpenVPN..."

# Asegurar que las claves y directorios necesarios existen
mkdir -p /dev/net
if [ ! -c /dev/net/tun ]; then
    mkdir -p /dev/net
    mknod /dev/net/tun c 10 200
fi

# Generar certificados si no existen (para entrenamiento)
if [ ! -f /etc/openvpn/keys/ca.crt ]; then
    echo "Generando certificados CA y de servidor..."
    cd /etc/openvpn/easy-rsa
    ./easyrsa init-pki
    ./easyrsa --batch build-ca nopass
    ./easyrsa --batch gen-req server nopass
    ./easyrsa --batch sign-req server server
    ./easyrsa gen-dh
    openvpn --genkey --secret ta.key

    # Copiar archivos generados a la ubicación esperada por OpenVPN
    cp pki/ca.crt /etc/openvpn/keys/
    cp pki/private/server.key /etc/openvpn/keys/
    cp pki/issued/server.crt /etc/openvpn/keys/
    cp pki/dh.pem /etc/openvpn/keys/
    cp ta.key /etc/openvpn/keys/
fi

# Configurar IP forwarding
echo 1 > /proc/sys/net/ipv4/ip_forward

# Configurar NAT para la red VPN
iptables -t nat -A POSTROUTING -s 10.8.0.0/24 ! -d 10.8.0.0/24 -j MASQUERADE

# Iniciar OpenVPN
echo "Iniciando OpenVPN server..."
openvpn --config /etc/openvpn/server.conf --daemon

# Esperar a que el servicio se inicie
sleep 3

# Verificar que el servicio esté corriendo
if pgrep openvpn > /dev/null; then
    echo "OpenVPN iniciado exitosamente en puerto 1194/udp"
else
    echo "Error: OpenVPN no se inició correctamente"
    exit 1
fi

# Mostrar información de conexión
echo ""
echo "Información de conexión VPN:"
echo "  Servidor: $(hostname -I | awk '{print $1}')"
echo "  Puerto: 1194/udp"
echo "  Protocolo: UDP"
echo "  Subnet VPN: 10.8.0.0/24"
echo ""

# Mantener el contenedor en ejecución y verificar el servicio
while true; do
    sleep 30
    if ! pgrep openvpn > /dev/null; then
        echo "OpenVPN se detuvo, reiniciando..."
        openvpn --config /etc/openvpn/server.conf --daemon
        sleep 3
    fi
done