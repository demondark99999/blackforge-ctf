FROM alpine:latest

# Instalar OpenVPN y herramientas útiles
RUN apk add --no-cache \
    openvpn \
    easy-rsa \
    iptables \
    bash

# Crear directorio para claves y configuración
RUN mkdir -p /etc/openvpn/keys /etc/openvpn/ccd /var/log/openvpn

# Copiar ejemplos de configuración (se sobrescribirán con los archivos montados)
COPY config/server.conf /etc/openvpn/server.conf
COPY config/vars /etc/openvpn/easy-rsa/vars

# Copiar script de inicio
COPY startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Exponer puerto UDP de OpenVPN
EXPOSE 1194/udp

# Volumen para configuración, claves y logs
VOLUME ["/etc/openvpn", "/var/log/openvpn"]

# Comando de inicio
CMD ["/usr/local/bin/startup.sh"]