# Arquitectura del Sistema - BlackForge Labs Instalación Omega

## Visión General

La Instalación Omega de BlackForge Labs representa un entorno de entrenamiento avanzado en ciberseguridad diseñado para simular amenazas persistentes avanzadas (APT) y proporcionar experiencia práctica en técnicas ofensivas y defensivas.

## Arquitectura General

El sistema sigue una arquitectura de microservicios distribuida utilizando contenedores Docker orquestados por Docker Compose. Se divide en dos entornos principales:

### Entorno Principal
- **Web Application**: Aplicación PHP basada en arquitectura MVC simplificada
- **Database Server**: MySQL 8.0 para almacenamiento relacional
- **Cache Layer**: Redis 7.0 para sesiones y caché de datos
- **Mail Server**: Postfix para envío y recepción de correos
- **FTP Server**: vsFTPd para transferencia de archivos
- **Reverse Proxy**: NGINX para balanceo de carga y terminación SSL
- **Firewall**: iptables para filtrado de tráfico perimetral
- **Monitoring Agent**: Script personalizado para recolección de métricas

### Entorno Secundario (Gestión y Seguridad)
- **Router**: Quagga para protocolos de enrutamiento OSPF/BGP
- **VPN Server**: OpenVPN para acceso remoto seguro
- **IDS/IPS**: Snort y Suricata para detección de intrusiones
- **SIEM**: Elasticsearch, Logstash, Graylog para gestión de logs y eventos
- **Filebeat**: Agente para envío de logs desde el entorno principal

## Comunicación entre Componentes

### Flujo de Datos Principal
1. **Cliente Web** ↔ **NGINX Proxy** (HTTP/HTTPS)
2. **NGINX Proxy** ↔ **Aplicación Web** (HTTP rápido)
3. **Aplicación Web** ↔ **Base de Datos** (MySQL Protocol)
4. **Aplicación Web** ↔ **Redis** (Redis Protocol)
5. **Aplicación Web** ↔ **Servicio de Correo** (SMTP)
6. **Aplicación Web** ↔ **Servidor FTP** (FTP/FTPS)

### Flujo de Seguridad y Monitoreo
1. **Tráfico de Red** ↔ **Router** (OSPF/BGP)
2. **Tráfico de Red** ↔ **IDS** (Port Mirroring / TAP)
3. **IDS** ↔ **SIEM** (Syslog / JSON over TCP)
4. **Entorno Principal** ↔ **Filebeat** ↔ **SIEM** (Logs de aplicación)
5. **Servicios** ↔ **Monitoring Agent** (Métricas personalizadas)
6. **SIEM** ↔ **Interfaz Web** (HTTP/HTTPS para visualización)

## Escalabilidad y Rendimiento

### Escalado Horizontal
- La aplicación web puede escalarse agregando más instancias detrás del proxy NGINX
- Redis puede configurarse en modo cluster para mayor capacidad
- Elasticsearch se escala horizontalmente mediante sharding y réplicas

### Balanceo de Carga
- NGINX actúa como balanceador de carga HTTP/TCP
- Se pueden configurar múltiples instancias de servicios detrás del proxy
- Salud de los servicios verificada mediante healthchecks personalizados

## Persistencia de Datos

### Almacenamiento Persistente
- **Base de Datos**: Volumen Docker `db_data` para datos MySQL
- **Cache**: Volumen Docker `cache_data` para persistencia de Redis (AOF/RDB)
- **Uploads**: Directorio montado `/var/www/html/uploads` para archivos de usuarios
- **Logs**: Volúmenes montados para logs de todos los servicios
- **Configuración**: Archivos de configuración persistentes en el host

### Estrategias de Respaldos
- Respaldos automatizados de base diaria mediante scripts
- Versionado de configuración mediante Git
- Archivos críticos replicados en almacenamiento secundario
- Procedure de restauración documentado y probado

## Seguridad del Sistema

### Capas de Defensa Implementadas
1. **Perímetro**: Firewall iptables con reglas de zona DMZ
2. **Red**: Segmentación mediante Docker networks y puertos expuestos limitados
3. **Aplicación**: Validación de entrada, salidas parametrizadas, CSP básico
4. **Datos**: Encriptación en reposo para información sensible (simulado)
5. **Monitoreo**: Logging centralizado y alertas en tiempo real
6. **Identidad**: Autenticación multifactor simulada, gestión de sesiones segura

### Vulnerabilidades Intencionales (Para Entrenamiento)
El entorno contiene múltiples vulnerabilidades intencionales para crear escenarios de entrenamiento realistas:
- Inyección SQL en múltiples puntos
- Cross-Site Scripting (XSS) reflejado y almacenado
- Inyección de comandos en funciones de sistema
- Path traversal en manejo de archivos
- Deserialización insegura de datos
- Vulnerabilidades de carrera (race conditions)
- Configuraciones por defecto débiles
- Exposición de información sensible en mensajes de error
- Cabezas de seguridad faltantes o mal configuradas
- Tokens CSRF implementados incorrectamente
- Validación de entrada insuficiente

## Tecnologías Utilizadas

| Componente | Tecnología | Versión | Propósito |
|------------|------------|---------|-----------|
| Orquestación | Docker | 24.0.0 | Contenerización de servicios |
| Orquestación | Docker Compose | v2.20.0 | Gestión de multi-contenedores |
| Web Server | NGINX | 1.25.0 | Proxy inverso, SSL termination |
| App Language | PHP | 8.2.0 | Lógica de aplicación principal |
| Database | MySQL | 8.0.34 | Almacenamiento relacional principal |
| Cache | Redis | 7.0.12 | Sesiones y caché de datos |
| Mail Server | Postfix | 3.7.2 | Envío y recepción de correo |
| FTP Server | vsFTPd | 3.1.3 | Transferencia de archivos |
| Routing | Quagga | 1.2.4 | Protocolos OSPF/BGP |
| VPN | OpenVPN | 2.6.0 | Acceso remoto seguro |
| IDS | Snort | 3.0.0 | Detección de intrusiones signature-based |
| IDS/IPS | Suricata | 6.0.0 | Detección de intrusiones multi-hilo |
| SIEM | Elasticsearch | 8.0.0 | Almacenamiento y búsqueda de logs |
| SIEM | Logstash | 8.0.0 | Procesamiento y enriquecimiento de logs |
| SIEM | Graylog | 5.0.0 | Visualización y análisis de logs |
| Log Shipper | Filebeat | 8.0.0 | Envío de logs desde agentes |
| Language UI | HTML5/CSS3/JS | Estándar | Interfaz de usuario |
| Language API | PHP | 8.2.0 | Endpoints REST internos |
| Version Control | Git | 2.40.0 | Gestión de código fuente |
| Documentation | Markdown | — | Documentación técnica |

## Patrones de Diseño Utilizados

### Arquitectura de Software
- **Front Controller**: index.php como punto de entrada único
- **Layered Architecture**: Separación de presentación, lógica y datos
- **Dependency Injection**: Contenedor simple para servicios de base de datos
- **Plugin System**: Arquitectura extensible para funcionalidades adicionales
- **Template Method**: Plantillas base para páginas y correos
- **Strategy**: Diferentes estrategias de autenticación y autorización

### Patrones de Enterprise
- **Data Access Object**: Clase Database para abstracción de PDO
- **Service Layer**: Servicios de negocio encapsulados en funciones estáticas
- **Repository Pattern**: Métodos de acceso a datos centralizados
- **Observer Pattern**: Sistema de logging y eventos de seguridad
- **Lazy Loading**: Carga bajo demanda de recursos no críticos

### Patrones de Seguridad (Versiones Vulnerables Intencionalmente)
- **Broken Access Control**: Verificaciones de autorización inconsistentes
- **Cryptographic Failures**: Uso de algoritmos débiles (MD5, SHA1)
- **Injection**: SQL, Command, XSS
- **Insecure Design**: Flujo de trabajo que permite escalada de privilegios
- **Security Misconfiguration**: Configuraciones por defecto inseguras
- **Vulnerable Components": Dependencias con vulnerabilidades conocidas (simuladas)
- **Identification and Authentication Failures": Autenticación débil, session fixation
- **Software and Data Integrity Failures": Falta de verificación de integridad
- **Security Logging and Monitoring Failures": Lagunas en el monitoreo intencionales
- **Server-Side Request Forgery (SSRF)": Endpoints que permiten peticiones arbitrarias

## Guía de Mantenimiento

### Procedimientos Rutinarios
1. **Monitoreo diario**: Verificar healthchecks de todos los servicios
2. **Revisión de logs**: Analizar eventos de seguridad en Graylog
3. **Actualización de firmas IDS**: actualizar reglas Snort/Suricata semanalmente
4. **Respaldos de configuración**: hacer commit de cambios en Git
5. **Limpieza de logs antiguos**: eliminar logs >30 días según política
6. **Pruebas de recuperación**: verificar procedimientos de restauración mensuales

### Escalamiento y Actualizaciones
1. **Actualización de imágenes**: docker-compose pull seguido de docker-compose up -d
2. **Migración de base de datos**: scripts de migración en /servicios/db/migrations/
3. **Cambios de configuración**: editando archivos en /infra/ y /servicios/*/config/
4. **Adición de nuevos servicios**: crear nuevo Dockerfile y agregar a docker-compose.yml
5. **Escalado de servicios**: modificar replicas en docker-compose.override.yml

## Diagrama de Arquitectura

```
[Internet]
     ��� � � ↓ HTTPS/HTTP
[NGINX Proxy (Puerto 80/443)]
     ��� � � ↓ HTTP Interno
[Web App] ←→ [MySQL Database]
     ��� � � ↓ Redis Protocol
[Redis Cache]
     ��� � � ↓ SMTP
[Postfix Mail]
     ��� � � ↓ FTP
[vsFTPd Server]
     ��� � � ↓ Syslog
[Filebeat Agent] → [Logstash] → [Elasticsearch] ←[Graylog UI]
                                          ��� � � ↓
[IDS Snort/Suricata] ←[Port Mirror]← [Quagga Router]
     ��� � � ↓ VPN Tunnel
[OpenVPN Server] ←→ [Usuario Administrador]
     ��� � � ↓ HTTP Interno
[Admin Panel] ←→ [Servicios Internos de Gestión]

[Red Interna Docker]
[Web App] ←→ [Monitoring Agent] (métricas personalizadas)
```

## Consideraciones de Despliegue

### Requisitos del Sistema
- Host Linux con kernel 5.4+
- Docker Engine 24.0+
- Docker Compose v2.20.0+
- Minimum 4GB RAM (8GB recomendado)
- Minimum 25GB SSD almacenamiento
- CPU con soporte para virtualización (VT-x/AMD-V)

### Pasos de Despliegue
1. Verificar requisitos del sistema
2. Clonar repositorio
3. Copiar .env.example a .env y configurar variables
4. Ejecutar ./scripts/deploy.sh
5. Verificar healthchecks con ./scripts/healthcheck.sh
6. Acceder a http://localhost para comenzar

### Variables de Configuración Críticas
- `DB_ROOT_PASSWORD`: Contraseña de root de MySQL
- `DB_PASSWORD`: Contraseña del usuario de aplicación
- `REDIS_PASSWORD`: Contraseña de Redis
- `SMTP_*`: Credenciales para servidor de correo
- `FTP_*`: Credenciales para servidor FTP
- `APP_KEY`: Clave para encriptación de sesiones
- `SESSION_SECRET`: Secreto para firmar sesiones
- `CSRF_TOKEN_SECRET`: Secreto para tokens CSRF

## Historial de Cambios

Ver archivo CHANGELOG.md para historial detallado de versiones.

## Contribuir

Ver archivo CONTRIBUTING.md para guidelines de contribución.

## Licencia

Este proyecto está licenciado bajo los términos de la licencia MIT - ver archivo LICENSE para detalles.