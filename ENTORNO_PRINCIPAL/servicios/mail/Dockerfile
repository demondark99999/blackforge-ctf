FROM catatnight/postfix

# Configuración de Postfix para entorno de entrenamiento
COPY main.cf /etc/postfix/main.cf
COPY master.cf /etc/postfix/master.cf

# Exponer puertos SMTP
EXPOSE 25 587

# Volumen para cola de correo y logs
VOLUME /var/spool/postfix
VOLUME /var/log/mail

# Comando de inicio
CMD ["postfix", "start-fg"]