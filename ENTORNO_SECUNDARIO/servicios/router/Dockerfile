FROM quay.io/frrouting/frr:9.0

# Instalar paquetes adicionales útiles para entrenamiento
RUN apt-get update && apt-get install -y \
    net-tools \
    iproute2 \
    dnsutils \
    telnet \
    && rm -rf /var/lib/apt/lists/*

# Copiar configuración de FRR
COPY config/frr.conf /etc/frr/frr.conf
COPY config/vtysh.conf /etc/frr/vtysh.conf
COPY config/bgpd.conf /etc/frr/bgpd.conf
COPY config/ospfd.conf /etc/frr/ospfd.conf
COPY config/ripd.conf /etc/frr/ripd.conf

# Copiar script de inicio
COPY startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Exponer puertos de protocolos de routing
EXPOSE 2601 2602 2603 2604

# Volumen para logs y configuración
VOLUME ["/etc/frr", "/var/log/frr"]

# Comando de inicio
CMD ["/usr/local/bin/startup.sh"]