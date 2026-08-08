# Historial de Cambios - BlackForge Labs Instalación Omega

Todos los cambios significativos en el proyecto serán documentados en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/),
y este proyecto se adhiere a [Versionamiento Semántico](https://semver.org/lang/es/).

## [Unreleased]
### Agregado
- Soporte para escaneo de vulnerabilidades con Trivy en pipeline de CI
- Nuevos escenarios de entrenamiento para ataques a cadena de suministro
- Documentación detallada de técnicas de evasión de detección
- Integración inicial con plataformas de amenazas inteligencia (MISP)

### Cambiado
- Actualización de imágenes base a versiones más recientes de seguridad
- Mejora en la documentación de procedimientos de respuesta a incidentes
- Optimización de scripts de despliegue para tiempos de inicio más rápidos
- Actualización de dependencias de PHP a versiones soportadas

### Eliminado
- Eliminado soporte para protocolos TLS 1.0 y 1.1 en configuraciones NGINX
- Removido ejemplos de código inseguro que podían confundir a usuarios recién llegados

### Seguridad
- Parcheada vulnerabilidad de información exposiva en endpoints de salud
- Actualizado los algoritmos de hash de contraseñas a bcrypt en versión de desarrollo
- Fortalecido las recomendaciones de configuración de firewalls perimetrales

## [1.2.0] - 2024-08-07
### Agregado
- Nuevos módulos de plugin para análisis de comportamiento de usuarios (UEBA)
- Integración de framework de pruebas de seguridad OWASP ZAP
- Documentación ampliada sobre técnicas de ingeniería social en entrenamientos
- Nuevos ejercicios específicos para seguridad de infraestructura en la nube (AWS/Azure)
- Scripts automatizados para generación de reportes de ejercicios
- Galería mejorada con funcionalidad de búsqueda avanzada y filtrado por metadatos

### Cambiado
- Mejorado el sistema de manejo de sesiones con rotación de claves más frecuente
- Actualizado los diseños de desafíos CTF para incluir más tipos de vulnerabilidades
- Mejorada la documentación de instalación para entornos con proxies corporativos
- Optimizado los healthchecks para reducir falsos positivos en monitoreo
- Actualizada la guía de usuario con mejores prácticas para entornos de trabajo remoto

### Eliminado
- Eliminado soporte para navegadores IE11 y versiones antiguas de Edge
- Removido dependencias deprecated que generaban advertencias en composer

### Seguridad
- Parcheada exposición accidental de variables de entorno en mensajes de error
- Actualizado algoritmos de cifrado para usar AES-256-GCM donde es apropiado
- Fortalecido las recomendaciones de política de contraseñas basado en NIST 800-63B
- Mejorado el manejo de errores para evitar divulgación de información sensible

## [1.1.0] - 2024-05-15
### Agregado
- Entorno secundario completo para entrenamiento en seguridad de redes y gestión
- Soporte completo para protocolos de enrutamiento OSPF y BGP usando Quagga
- Implementación de sistema de detección de intrusiones dual (Snort + Suricata)
- Plataforma SIEM completa con Elasticsearch, Logstash, Graylog y Filebeat
- Nuevos ejercicios específicos para análisis de tráfico de red y forensia digital
- Documentación detallada de técnicas de evasión de IDS/IPS y tunneling
- Soporte para simulación de ataques a protocolos de routing (RIPv2 attacks)
- Nuevos módulos de entrenamiento para seguridad de VoIP y comunicaciones unificadas

### Cambiado
- Reorganizacion completa de la estructura de documentos para mejor navegabilidad
- Actualizado todos los Dockerfiles para usar versiones base más recientes y seguras
- Mejorada la documentación de procedimientos de despliegue y recuperación
- Actualizada la guía de usuario con secciones ampliadas sobre técnicas defensivas
- Mejorados los healthchecks de todos los servicios para detección más temprana de problemas
- Estandarizado los mensajes de log y formato de timestamps en todo el sistema

### Eliminado
- Eliminado soporte para versiones de PHP anteriores a 7.4
- Removido código duplicado y funcionalidades redundantes identificadas en auditoría
- Eliminado dependencias de frontend no utilizadas que aumentaban el tamaño del bundle

### Seguridad
- Parcheada múltiples vulnerabilidades de información exposiva identificadas en pentesting interno
- Actualizado los mecanismos de manejo de sesiones para prevenir fijación de sesión
- Fortalecido las recomendaciones de configuración de contraseñas y pol de caducidad
- Mejorado el aislamiento de contenedores mediante uso de namespaces y capabilities reducidos
- Actualizado las imágenes base para incluir los últimos parches de seguridad

## [1.0.0] - 2024-01-10
### Agregado
- Entorno principal completo de entrenamiento en ciberseguridad
- Aplicación web basada en PHP 8.2 con arquitectura modular
- Base de datos MySQL 8.0 con esquema completo para entrenamiento
- Cache Redis 7.0 para almacenamiento de sesiones y datos frecuentemente accedidos
- Servicio de correo Postfix 3.7.2 para notificaciones y comunicación
- Servicio de FTP vsFTPd 3.1.3 para transferencia de archivos
- Proxy inverso NGINX 1.25.0 con terminación SSL y balanceo de carga
- Firewall perimetral con iptables para control de acceso de red
- Sistema de monitoreo personalizado para recolección de métricas de entrenamiento
- Documentación técnica completa incluyendo arquitectura, historia y guía de usuario
- Referencia de API completa para interacción programática con el entorno
- Sistema de plugins extensible para funcionalidades adicionales
- Sistema de temas para personalización de la interfaz de usuario
- Sistema de plantillas para correos electrónicos y páginas de contenido
- Historial de git simulado que muestra años de desarrollo activo
- Issues y pull requests simulados para mostrar colaboración activa
- Workflows de GitHub Actions para CI/CD y seguridad
- Licencia MIT, archivos de contribución y conducta, y política de seguridad

### Cambiado
- Ninguna (versión inicial)

### Eliminado
- Ninguna (versión inicial)

### Seguridad
- Vulnerabilidades intencionales diseñadas para entrenamiento CTF:
  - Inyección SQL en múltiples puntos de entrada
  - Cross-Site Scripting (XSS) reflejado y almacenado
  - Inyección de comandos en funciones de sistema
  - Path traversal en manejo de archivos de subida
  - Deserialización insecurade datos en endpoints de API
  - Vulnerabilidades de fuerza bruta en autenticación
  - Exposición de información sensible en mensajes de error
  - Configuraciones por defecto inseguras en múltiples servicios
  - Tokens CSRF implementados incorrectamente para entrenamiento
  - Validación de entrada insuficiente en diversos puntos de entrada
  - Información de versión expuesta que podría asistir a atacantes
  - Encabezados de seguridad faltantes o mal configurados
  - Sesiones con timeout excesivo o falta de rotación adecuada
  - Archivos de configuración con permisos inadecuados
  - Exposición de rutas absolutas en mensajes de error
  - Posibilidad de ataque de retransmisión FTP (FTP bounce)
  - Configuración de relay abierto en servidor de correo para ejercicios
  - Puerto SSH expuesto para ejercicios de fuerza bruta
  - Acceso a base de datos desde cualquier IP para ejercicios de inyección
  - Acceso a Redis desde cualquier IP para ejercicios de acceso no autorizado