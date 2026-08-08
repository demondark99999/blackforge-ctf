FROM mysql:8.0

# Configuración de MySQL
ENV MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
ENV MYSQL_DATABASE=forge_db
ENV MYSQL_USER=forge_user
ENV MYSQL_PASSWORD=${DB_PASSWORD}

# Copiar scripts de inicialización
COPY init/ /docker-entrypoint-initdb.d/

# Exponer puerto MySQL
EXPOSE 3306

# Volumen para persistencia de datos
VOLUME /var/lib/mysql

# Comando de inicio
CMD ["mysqld"]