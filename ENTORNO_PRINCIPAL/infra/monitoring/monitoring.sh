#!/bin/bash
# Script de monitoreo para BlackForge Labs - Instalación Omega
# Recopila y expone métricas del sistema para el entorno de entrenamiento CTF

set -e

# Configuración
INTERVAL=${MONITORING_INTERVAL:-15}
PORT=${METRICS_PORT:-9100}
LOG_FILE="/var/log/monitoring/monitoring.log"

# Crear directorio de logs si no existe
mkdir -p /var/log/monitoring

echo "Iniciando servicio de monitoreo en puerto $PORT..."
echo "Intervalo de recolección: $INTERVAL segundos"
echo "Logs se escribirán en: $LOG_FILE"

# Función para generar métricas en formato Prometheus
generate_metrics() {
    local timestamp=$(date +%s)

    # Métricas del sistema
    local cpu_usage=$(top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk '{print 100 - $1}')
    local mem_total=$(free | grep Mem | awk '{print $2}')
    local mem_used=$(free | grep Mem | awk '{print $3}')
    local mem_usage=$(echo "scale=2; $mem_used * 100 / $mem_total" | bc)
    local disk_usage=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')

    # Métricas de red (simuladas para el entorno de contenedor)
    local rx_bytes=$(cat /proc/net/dev | grep eth0 | tr -s ' ' | cut -d' ' -f3)
    local tx_bytes=$(cat /proc/net/dev | grep eth0 | tr -s ' ' | cut -d' ' -f11)

    # Contadores de procesos
    local process_count=$(ps aux | wc -l)

    # Salida en formato Prometheus
    cat << EOF
# HELP blackforge_cpu_usage_percentage CPU usage percentage
# TYPE blackforge_cpu_usage_percentage gauge
blackforge_cpu_usage_percentage $cpu_usage

# HELP blackforge_memory_usage_percentage Memory usage percentage
# TYPE blackforge_memory_usage_percentage gauge
blackforge_memory_usage_percentage $mem_usage

# HELP blackforge_disk_usage_percentage Disk usage percentage
# TYPE blackforge_disk_usage_percentage gauge
blackforge_disk_usage_percentage $disk_usage

# HELP blackforge_network_receive_bytes_total Network received bytes
# TYPE blackforge_network_receive_bytes_total counter
blackforge_network_receive_bytes_total $rx_bytes

# HELP blackforge_network_transmit_bytes_total Network transmitted bytes
# TYPE blackforge_network_transmit_bytes_total counter
blackforge_network_transmit_bytes_total $tx_bytes

# HELP blackforge_process_count Total number of processes
# TYPE blackforge_process_count gauge
blackforge_process_count $process_count

# HELP blackforge_timestamp_seconds Unix timestamp
# TYPE blackforge_timestamp_seconds gauge
blackforge_timestamp_seconds $timestamp
EOF
}

# Función para iniciar un servidor HTTP simple que expone métricas
start_metrics_server() {
    while true; do
        {
            echo -e "HTTP/1.1 200 OK\r\nContent-Type: text/plain; charset=utf-8\r\n\r\n$(generate_metrics)"
        } | nc -l -p $PORT -q 1
        # Pequeña delay para evitar consumo excesivo de CPU
        sleep 0.1
    done &
}

# Iniciar servidor de métricas en background
start_metrics_server

# Mantener el contenedor en ejecución y escribir logs periódicamente
echo "$(date): Servicio de monitoreo iniciado correctamente" >> "$LOG_FILE"

while true; do
    sleep $INTERVAL
    # Generar y guardar métricas en log
    generate_metrics >> "$LOG_FILE" 2>&1
    echo "---" >> "$LOG_FILE"
done