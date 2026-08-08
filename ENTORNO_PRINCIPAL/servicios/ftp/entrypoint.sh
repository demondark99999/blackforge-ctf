#!/bin/bash
# Entry point para vsftpd en contenedor Docker
# BlackForge Labs - Instalación Omega

set -e

# Esperar a que la base de datos esté disponible (si fuera necesario)
# En este caso, vsftpd no depende directamente de la DB, pero podemos añadir una espera genérica
sleep 5

# Crear directorio de usuarios virtuales si no existe
mkdir -p /home/vsftpd

# Iniciar vsftpd en modo foreground
/usr/sbin/vsftpd /etc/vsftpd/vsftpd.conf