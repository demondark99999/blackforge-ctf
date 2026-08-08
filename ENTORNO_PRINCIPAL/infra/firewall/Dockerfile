FROM alpine:latest

# Instalar iptables y herramientas de red
RUN apk add --no-cache iptables ip6tables iproute2

# Copiar scripts de firewall
COPY firewall.sh /usr/local/bin/firewall.sh
COPY iptables.rules /etc/iptables.rules

# Hacer ejecutable el script
RUN chmod +x /usr/local/bin/firewall.sh

# Exponer ningún puerto directamente (el firewall gestiona el tráfico)
# Pero necesitamos capacidad para modificar redes
CAP_ADD NET_ADMIN
CAP_ADD NET_RAW
CAP_ADD SYS_MODULE

# Volumen para reglas y logs
VOLUME /etc/iptables
VOLUME /var/log

# Comando de entrada
ENTRYPOINT ["/usr/local/bin/firewall.sh"]