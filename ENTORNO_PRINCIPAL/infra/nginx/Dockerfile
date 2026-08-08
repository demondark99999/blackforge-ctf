FROM nginx:1.25-alpine

# Copiar configuración personalizada
COPY nginx.conf /etc/nginx/nginx.conf
COPY sites-available/ /etc/nginx/sites-available/
COPY ssl/ /etc/nginx/ssl/

# Exponer puertos HTTP y HTTPS
EXPOSE 80 443

# Volumen para logs
VOLUME /var/log/nginx

# Comando de inicio
CMD ["nginx", "-g", "daemon off;"]