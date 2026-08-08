FROM redis:7.0-alpine

# Configuración de Redis
COPY redis.conf /usr/local/etc/redis/redis.conf

# Exponer puerto Redis
EXPOSE 6379

# Volumen para persistencia de datos
VOLUME /data

# Comando de inicio
CMD ["redis-server", "/usr/local/etc/redis/redis.conf"]