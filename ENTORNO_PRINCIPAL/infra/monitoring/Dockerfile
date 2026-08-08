FROM alpine:latest

# Instalar herramientas de monitoreo básicas
RUN apk add --no-cache \
    curl \
    bash \
    procps \
    sysstat

# Copiar scripts de monitoreo
COPY monitoring.sh /usr/local/bin/monitoring.sh
COPY prometheus.yml /etc/prometheus/prometheus.yml

# Hacer ejecutable el script
RUN chmod +x /usr/local/bin/monitoring.sh

# Exponer puerto para métricas (si se usa Prometheus node exporter o similar)
EXPOSE 9100

# Volumen para datos y logs
VOLUME /var/lib/monitoring
VOLUME /var/log/monitoring

# Variables de entorno para configuración
ENV MONITORING_INTERVAL=15
ENV METRICS_PORT=9100
ENV PROMETHEUS_PORT=9090

# Comando de entrada
ENTRYPOINT ["/usr/local/bin/monitoring.sh"]