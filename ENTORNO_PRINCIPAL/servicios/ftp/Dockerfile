FROM fauria/vsftpd

# Configuración de vsftpd para entorno de entrenamiento
COPY vsftpd.conf /etc/vsftpd/vsftpd.conf
COPY vsftpd.virtual_users.txt /etc/vsftpd/virtual_users.txt
COPY user_list /etc/vsftpd.user_list

# Exponer puertos FTP
EXPOSE 20 21 21100-21110

# Volumen para datos FTP y logs
VOLUME /home/vsftpd
VOLUME /var/log/vsftpd

# Variables de entorno para usuarios virtuales
ENV FTP_USER=${FTP_USER}
ENV FTP_PASSWORD=${FTP_PASSWORD}
ENV PASV_ADDRESS=${PASV_ADDRESS}
ENV PASV_MIN_PORT=${PASV_MIN_PORT}
ENV PASV_MAX_PORT=${PASV_MAX_PORT}

# Script de entrada personnalizado
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]