FROM jasonish/snort:latest

# Instalar paquetes adicionales útiles
RUN apt-get update && apt-get install -y \
    net-tools \
    iproute2 \
    && rm -rf /var/lib/apt/lists/*

# Copiar configuración y reglas personalizadas
COPY config/snort.conf /etc/snort/snort.conf
COPY rules/ /etc/snort/rules/

# Copiar script de inicio
COPY startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Exponer puerto para la interfaz web de BASE (si se instala)
EXPOSE 80

# Volumen para configuración, reglas y logs
VOLUME ["/etc/snort", "/etc/snort/rules", "/var/log/snort"]

# Comando de inicio
CMD ["/usr/local/bin/startup.sh"]