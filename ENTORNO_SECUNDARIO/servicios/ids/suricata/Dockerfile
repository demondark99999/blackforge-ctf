FROM jasonish/suricata:latest

# Instalar paquetes adicionales útiles
RUN apt-get update && apt-get install -y \
    net-tools \
    iproute2 \
    && rm -rf /var/lib/apt/lists/*

# Copiar configuración y reglas personalizadas
COPY config/suricata.yaml /etc/suricata/suricata.yaml
COPY rules/ /etc/suricata/rules/

# Copiar script de inicio
COPY startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Exponer puerto para acceso a logs y estadísticas
EXPOSE 80

# Volumen para configuración, reglas y logs
VOLUME ["/etc/suricata", "/etc/suricata/rules", "/var/log/suricata"]

# Comando de inicio
CMD ["/usr/local/bin/startup.sh"]