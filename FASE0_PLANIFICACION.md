# FASE 0 — PLANIFICACIÓN
## Diseño completo del laboratorio CTF INSANE

### 1. ARQUITECTURA GENERAL
El laboratorio CTF se basa en una arquitectura de microservicios distribuida utilizando Docker y Docker Compose para orquestación. Consiste en dos entornos independientes pero narrativamente conectados:
- **Entorno Principal**: Aplicación web vulnerable basada en un CMS modificado con múltiples servicios auxiliares
- **Entorno Secundario**: Infraestructura de red simulada con servicios de administración y monitoring

### 2. ÁRBOL COMPLETO DEL PROYECTO
```
ctf-lab-insane/
├── DOCUMENTACION/
│   ├── ARQUITECTURA.md
│   ├── HISTORIA_LABORATORIO.md
│   ├── GUIA_USUARIO.md
│   └── API_REFERENCE.md
├── ENTORNO_PRINCIPAL/
│   ├── docker-compose.yml
│   ├── .env
│   ├── servicios/
│   │   ├── web-app/
│   │   │   ├── Dockerfile
│   │   │   ├── src/
│   │   │   │   ├── index.php
│   │   │   │   ├── config/
│   │   │   │   │   ├── database.php
│   │   │   │   │   └── security.php
│   │   │   │   ├── modules/
│   │   │   │   │   ├── auth/
│   │   │   │   │   ├── upload/
│   │   │   │   │   └── admin/
│   │   │   │   ├── themes/
│   │   │   │   │   └── default/
│   │   │   │   ├── plugins/
│   │   │   │   │   ├── seo/
│   │   │   │   │   └── backup/
│   │   │   │   ├── uploads/
│   │   │   │   └── .htaccess
│   │   │   ├── scripts/
│   │   │   │   ├── install.sh
│   │   │   │   ├── update.sh
│   │   │   │   └── backup.sh
│   │   │   └── tests/
│   │   │       ├── unit/
│   │   │       └── integration/
│   │   ├── db/
│   │   │   ├── Dockerfile
│   │   │   ├── init/
│   │   │   │   ├── 01_schema.sql
│   │   │   │   └── 02_data.sql
│   │   │   └── backups/
│   │   ├── cache/
│   │   │   └── Dockerfile
│   │   ├── mail/
│   │   │   └── Dockerfile
│   │   └── ftp/
│   │       └── Dockerfile
│   └── infra/
│       ├── nginx/
│       │   ├── Dockerfile
│       │   ├── config/
│       │   │   ├── nginx.conf
│       │   │   └── sites-available/
│       │   │       └── ctf-site.conf
│       │   └── logs/
│       ├── firewall/
│       │   ├── Dockerfile
│       │   └── config/
│       │       └── iptables.rules
│       └── monitoring/
│           ├── Dockerfile
│           └── config/
│               ├── prometheus.yml
│               └── grafana/
│                   └── dashboards/
├── ENTORNO_SECUNDARIO/
│   ├── docker-compose.yml
│   ├── .env
│   ├── servicios/
│   │   ├── router/
│   │   │   ├── Dockerfile
│   │   │   └── config/
│   │   │       ├── quagga/
│   │   │       └── zebra.conf
│   │   ├── vpn/
│   │   │   ├── Dockerfile
│   │   │   └── config/
│   │   │       ├── openvpn/
│   │   │       │   ├── server.conf
│   │   │       │   └── clients/
│   │   │       └── easy-rsa/
│   │   ├── ids/
│   │   │   ├── Dockerfile
│   │   │   └── config/
│   │   │       ├── snort/
│   │   │       │   ├── rules/
│   │   │       │   └── snort.conf
│   │   │       └── suricata/
│   │   │       │   ├── rules/
│   │   │       │   └── suricata.yaml
│   │   └── logging/
│   │       ├── Dockerfile
│   │       └── config/
│   │           ├── elk/
│   │           │   ├── elasticsearch.yml
│   │           │   ├── logstash.conf
│   │           │   └── filebeat.yml
│   │           └── graylog/
│   │               └── server.conf
│   └── infra/
│       ├── network/
│       │   ├── docker-networks.conf
│       │   └── vlans/
│   │       ├── vlan10.conf
│   │       └── vlan20.conf
│   └── scripts/
│       ├── deploy.sh
│       ├── backup.sh
│       └── restore.sh
├── REPOSITORIO_GIT/
│   ├── .git/
│   ├── .gitignore
│   ├── README.md
│   ├── CHANGELOG.md
│   ├── LICENSE
│   ├── CONTRIBUTING.md
│   ├── CODE_OF_CONDUCT.md
│   ├── SECURITY.md
│   ├── .github/
│   │   └── workflows/
│   │       ├── ci.yml
│   │       └── security-scan.yml
│   ├── docs/
│   │   ├── architecture-decision-records/
│   │   └── api/
│   ├── src/
│   │   ├── lib/
│   │   ├── utils/
│   │   └── vendors/
│   └── scripts/
│       ├── release.sh
│       ├── changelog.sh
│       └── contributors.sh
�└── FLAGS/
    └── flag.txt (generado al final)
```

### 3. DIAGRAMA DE SERVICIOS
**Entorno Principal:**
- Cliente Web (Navegador) ↔ NGINX Proxy ↔ Aplicación Web (PHP/MySQL)
                                          � ↓
                                    Servicio de Cache (Redis)
                                          � ↓
                                    Servicio de Email (Postfix)
                                          � ↓
                                    Servicio FTP (vsFTPd)
**Entorno Secundario:**
- Router (Quagga) ↔ VPN Server (OpenVPN) ↔ IDS (Snort/Suricata) ↔ Sistema de Logging (ELK/Graylog)
                                          � ↓
                                    Firewall (iptables)

### 4. DEPENDENCIAS
**Entorno Principal:**
- PHP 8.2+
- MySQL 8.0
- Redis 7.0
- NGINX 1.25
- Postfix
- vsFTPd
- Composer (para gestión de paquetes PHP)

**Entorno Secundario:**
- Quagga (para routing)
- OpenVPN
- Snort 3.0 + Suricata 6.0
- Elasticsearch 8.0
- Logstash 8.0
- Filebeat 8.0
- Graylog 5.0

### 5. FLUJO GENERAL
1. Usuario accede al portal web principal mediante navegador
2. NGINX actúa como proxy inverso y balanceador de carga
3. La aplicación web procesa las solicitudes y accede a la base de datos MySQL
4. Las operaciones intensivas se caché en Redis para mejorar rendimiento
5. Las notificaciones se envían através del servicio de correo
6. Los archivos subidos se almacenan y pueden ser accedidos vía FTP
7. Paralelamente, el entorno secundario monitoriza el tráfico de red
8. El IDS analiza paquetes en busca de patrones maliciosos
9. Los logs se envían al sistema centralizado de logging para correlación
10. El VPN permite acceso seguro a servicios de administración interna

### 6. HISTORIA COMPLETA DEL LABORATORIO
**Nombre del Laboratorio:** "BlackForge Labs - Instalación Omega"

**Historia de Fondo:**
BlackForge Labs fue fundado en 2018 como una instalación de investigación privada especializada en ciberseguridad ofensiva y defensiva. La Instalación Omega, construida en 2022, representa su proyecto más ambicioso: un entorno de entrenamiento integral diseñado para simular ataques avanzados persistentes (APT) contra infraestructuras críticas.

La instalación fue diseñada por un equipo de ex-operadores de unidades de élite cibernéticas de diversos países, con experiencia en operaciones reales contra objetivos estatales y corporativos de alto valor. Cada componente del laboratorio fue cuidadosamente seleccionado para replicar fielmente las tecnologías, configuraciones y vulnerabilidades encontradas en entornos de producción reales.

Durante su operación, la Instalación Omega ha sido utilizada para:
- Entrenamiento de equipos de respuesta a incidentes de organizaciones Fortune 500
- Ejercicios conjuntos entre agencias de inteligencia aliadas
- Evaluación de herramientas de detección y respuesta ante amenazas (EDR/XDR)
- Desarrollo y prueba de técnicas de evasión avanzadas
- Certificación de profesionales en seguridad ofensiva de nivel experto

Recientemente, se detectó una infracción de seguridad interna que comprometió ciertos segmentos de la instalación. Como parte del protocolo de contingencia, se han aislado ciertos sistemas y se ha solicitado una evaluación de seguridad externa para identificar la extensión del compromiso y recomendar medidas de contención.

### 7. ORGANIZACIÓN DE CARPETAS
Ya detallada en el árbol del proyecto (sección 2)

### 8. CONVENCIONES DE NOMBRES
- **Directorios**: snake_case (ej: docker-compose.yml, src/modules/)
- **Archivos de configuración**: snake_case con extensión apropiada (ej: nginx.conf, config.php)
- **Scripts**: snake_case con extensión .sh (ej: deploy.sh, backup.sh)
- **Imágenes Docker**: lowercase con guiones (ej: blackforge-web-app, blackforge-db)
- **Contenedores**: lowercase con guiones (ej: web-app, database-server)
- **Redes Docker**: lowercase con guiones (ej: blackforge-net, monitoring-net)
- **Volúmenes**: lowercase con guiones (ej: web-data, db-backups)
- **Variables de entorno**: UPPER_SNAKE_CASE (ej: DB_HOST, REDIS_PORT)
- **Tablas de base de datos**: snake_case (ej: users, user_sessions, audit_logs)
- **Columnas**: snake_case (ej: id, username, email, created_at)
- **Archivos de log**: nombre-servicio.log (ej: web-app.log, auth.log)
- **Archivos de backup**: nombre-fecha-hora.ext (ej: db-backup-20240115-1430.sql)

### 9. TABLA DE TECNOLOGÍAS
| Categoría | Tecnología | Versión | Propósito |
|-----------|------------|---------|-----------|
| Servidor Web | NGINX | 1.25.0 | Proxy inverso, balanceo de carga, terminación SSL |
| Lenguaje Backend | PHP | 8.2.0 | Lógica de aplicación, procesamiento de formularios |
| Base de Datos | MySQL | 8.0.34 | Almacenamiento principal de datos relacionales |
| Cache | Redis | 7.0.12 | Almacenamiento en caché de sesiones y consultas frecuentes |
| Servidor de Correo | Postfix | 3.7.2 | Envío y recepción de correos electrónicos |
| Servidor FTP | vsFTPd | 3.1.3 | Transferencia de archivos |
| Sistema de Routing | Quagga | 1.2.4 | Protocolos de enrutamiento OSPF/BGP |
| VPN | OpenVPN | 2.6.0 | Acceso seguro remoto a servicios internos |
| IDS/IPS | Snort | 3.0.0 | Detección de intrusiones basada en firme |
| IDS/IPS Alternativo | Suricata | 6.0.0 | Detección de intrusiones multihilo |
| SIEM | Elasticsearch | 8.0.0 | Almacenamiento y búsqueda de logs |
| SIEM | Logstash | 8.0.0 | Procesamiento y enriquecimiento de logs |
| SIEM | Filebeat | 8.0.0 | Envío de logs desde agentes |
| SIEM | Graylog | 5.0.0 | Interfaz de búsqueda y visualización de logs |
| Orquestación | Docker | 24.0.0 | Contenedorización de servicios |
| Orquestación | Docker Compose | v2.20.0 | Definición y ejecución de aplicaciones multi-contenedor |
| Gestión de Paquetes PHP | Composer | 2.6.0 | Gestión de dependencias de PHP |
| Control de Versiones | Git | 2.40.0 | Control de código fuente |
| Lenguaje de Script | Bash | 5.2.0 | Automatización de tareas |

### 10. TABLA DE SERVICIOS
**Entorno Principal:**
| Servicio | Contenedor | Puerto | Descripción |
|----------|------------|--------|-------------|
| NGINX Proxy | nginx-proxy | 80, 443 | Proxy inverso y terminación SSL |
| Aplicación Web | web-app | 8080 | Aplicación PHP principal |
| Base de Datos | database | 3306 | MySQL principal |
| Cache | cache | 6379 | Redis para sesiones y caché |
| Correo | mail | 25, 587 | Postfix para envío de emails |
| FTP | ftp-server | 21, 20 | vsFTPd para transferencia de archivos |
| Monitoreo | monitoring-agent | - | Recopilador de métricas |

**Entorno Secundario:**
| Servicio | Contenedor | Puerto | Descripción |
|----------|------------|--------|-------------|
| Router | router | - | Quagga para OSPF/BGP |
| VPN Server | vpn-server | 1194 | OpenVPN para acceso remoto |
| IDS | ids-snort | - | Snort en modo promiscuo |
| IDS Alternativo | ids-suricata | - | Suricata en modo IPS |
| Elasticsearch | elasticsearch | 9200 | Almacenamiento de logs |
| Logstash | logstash | 5044 | Procesamiento de logs |
| Graylog | graylog | 9000, 12201 | Interfaz web y entrada de logs |
| Filebeat | filebeat-agent | - | Envío de logs desde entorno principal |

### 11. TABLA DE MÓDULOS (Aplicación Web)
| Módulo | Descripción | Estado | Vulnerabilidades Intencionales |
|--------|-------------|--------|-------------------------------|
| Auth | Autenticación y autorización de usuarios | Activo | Fuerza bruta, bypass de 2FA, JWT weaknesses |
| User Management | Gestión de perfiles y roles | Activo | Inyección SQL, privilegios escalables |
| Content Management | Creación y edición de contenido | Activo | XSS stored, upload de archivos maliciosos |
| File Upload | Subida y gestión de archivos | Activo | Path traversal, ejecución remota de código |
| API REST | Interfaz para integraciones externas | Activo | IDOR, rate limiting bypass |
| Admin Panel | Panel de administración del sistema | Activo | SSRF, deserialización insegura |
| Backup System | Sistema de respaldos automatizados | Activo | Inyección de comandos, race conditions |
| Notification System | Envío de alertas y notificaciones | Activo | Inyección de encabezados, XSS reflejado |
| Logging Module | Sistema de registro de eventos | Activo | Log injection, bypass de sanitización |
| Maintenance Mode | Modo de mantenimiento del sitio | Inactivo (activable) | Bypass de restricciones, información sensible |

### 12. TABLA DE COMPONENTES
**Infraestructura de Red:**
- VLAN 10: Red de gestión y administración (192.168.10.0/24)
- VLAN 20: Red de servicios públicos (192.168.20.0/24)
- VLAN 30: Red de aislada para análisis forense (192.168.30.0/24)
- VLAN 99: Red de cuarentena para sistemas comprometidos (192.168.99.0/24)

**Componentes de Seguridad:**
- WAF: ModSecurity con reglas OWASP CRS personalizadas
- HIDS: Wazhu para monitoreo de integridad de archivos
- Failsafe: Mecanismos de aislamiento automático ante detección de amenazas
- Honeytokens: Credenciales falsas y archivos sembrados para detección de intrusos
- Canary Tokens: URLs y documentos de seguimiento para alerta temprana

### 13. DIAGRAMA DE COMUNICACIÓN
```
[Usuario] 
     � ↓ HTTPS/HTTP
[NGINX Proxy (Entorno Principal)]
     � ↓ HTTP Interno
[Aplicación Web] ←→ [Base de Datos]
     � ↓ Redis
[Servicio de Cache]
     � ↓ SMTP
[Servicio de Correo]
     � ↓ FTP
[Servidor FTP]
     � ↓ Syslog
[Agente Filebeat] → [Logstash] → [Elasticsearch] ←[Graylog]
                                          � ↓
[IDS Snort/Suricata] ←[Espejo de Tráfico]← [Router]
     � ↓ VPN TUN
[Servidor VPN] ←→ [Usuario Administrador]
     � ↓ HTTP Interno
[Panel de Administración] ←→ [Servicios Internos]
                                
[Entorno Secundario - Red de Gestión]
[Router] ←→ [Firewall] ←→ [VPN Server] ←→ [IDS]
                                         � ↓
                                   [Sistema de Logging]
```

### 14. PLAN DE DESPLIEGUE
1. **Preparación**
   - Verificar requisitos del sistema (Docker, Docker Compose, git)
   - Clonar repositorio
   - Crear archivos .env desde ejemplos
   
2. **Construcción**
   - Ejecutar `docker-compose build` en ambos entornos
   - Descargar e inicializar imágenes base

3. **Inicialización**
   - Ejecutar `docker-compose up -d` en entorno principal
   - Esperar a que los servicios estén saludables (healthchecks)
   - Ejecutar scripts de instalación de la aplicación
   - Ejecutar `docker-compose up -d` en entorno secundario
   - Configurar routing entre entornos vía Docker network aliases

4. **Verificación**
   - Ejecutar healthchecks de todos los servicios
   - Probar conectividad entre servicios
   - Validar que la aplicación web responde correctamente
   - Confirmar que el entorno de logging recibe datos

5. **Acceso**
   - Proveer URL de acceso al usuario estándar
   - Proveer credenciales iniciales
   - Documentar procesos de escalamiento de privilegios

### 15. PLAN DE PRUEBAS
**Pruebas Unitarias:**
- Pruebas de validación de entrada en todos los módulos PHP
- Pruebas de manejo de errores en conexiones a base de datos
- Pruebas de sanitización de salida para prevenir XSS
- Pruebas de límites de velocidad en endpoints de API

**Pruebas de Integración:**
- Flujo completo de autenticación y autorización
- Integración entre aplicación web y servicio de caché
- Funcionamiento del sistema de logging distribuido
- Comunicación entre entorno principal y secundario

**Pruebas de Seguridad (Durante desarrollo):**
- Escaneo estático de código con PHPStan y Psalm
- Análisis de dependencias vulnerables con Composer Audit
- Pruebas de penetración manual en entorno de staging
- Validación de que las vulnerabilidades intencionales existen y son explotables
- Verificación de que las mitigaciones no intencionales funcionan correctamente

### 16. CRONOGRAMA DE GENERACIÓN
**Semana 1: Fase 0 - Planificación**
- Día 1: Definición de arquitectura y requisitos
- Día 2: Diseño de árbol de proyecto y organización de carpetas
- Día 3: Diseño de servicios y tecnologías
- Día 4: Creación de tablas de módulos, componentes y dependencias
- Día 5: Diseño de diagramas de comunicación y flujo de datos
- Día 6: Elaboración de historia del laboratorio y documentación
- Día 7: Revisión y ajuste del plan completo

**Semana 2: Fase 1 - Infraestructura**
- Día 8-9: Creación de Dockerfiles para todos los servicios
- Día 10-11: Diseño de docker-compose.yml para ambos entornos
- Día 12: Configuración de redes y volúmenes Docker
- Día 13: Creación de scripts de inicialización y despliegue
- Día 14: Configuración de variables de entorno y archivos .env examples

**Semana 3: Fase 2 - Aplicación**
- Día 15-18: Desarrollo de la aplicación web principal
- Día 19-20: Configuración de base de datos y datos iniciales
- Día 21-22: Implementación de sistemas de autenticación y autorización
- Día 23-24: Desarrollo de módulos de gestión de contenido y archivos
- Día 25-26: Implementación de APIs y sistemas de notificación
- Día 27-28: Creación de panel de administración y sistema de respaldos

**Semana 4: Fase 3 - Contenido**
- Día 29-30: Creación de historia coherente y documentación interna
- Día 31-32: Organización de directorios auxiliares y recursos
- Día 33-34: Configuración de logs históricos y backups simulados
- Día 35-36: Preparación de recursos internos y credenciales de prueba
- Día 37-38: Organización consistente de todos los componentes
- Día 39: Revisión de que todo tenga sentido dentro del proyecto

**Semana 5: Fase 4 - Repositorio Git**
- Día 40: Inicialización de repositorio Git
- Día 41-42: Creación de README, CHANGELOG, LICENSE y otros archivos estándar
- Día 43-44: Creación de estructura de branches y tags iniciales
- Día 45-46: Simulación de historial de commits con mensajes realistas
- Día 47-48: Creación de workflows de GitHub Actions y scripts de automatización
- Día 49-50: Simulación de issues y pull requests
- Día 51: Verificación de que el repositorio parece mantenido durante años

**Semana 6: Fase 5 - Segundo Entorno**
- Día 52-54: Diseño de arquitectura distinta para entorno secundario
- Día 55-57: Creación de servicios distintos (router, VPN, IDS, logging)
- Día 58-60: Desarrollo de configuración propia y scripts especializados
- Día 61-62: Creación de documentación propia para el entorno secundario
- Día 63-64: Integración narrativa con el laboratorio principal
- Día 65: Verificación de independencia técnica y conexión narrativa

**Semana 7: Fase 6 - Auditoría**
- Día 66-68: Verificación de existencia de todos los archivos referenciados
- Día 69-71: Corregir referencias rotas y variables inconsistentes
- Día 72-73: Validar que todos los scripts existen y son funcionales
- Día 74-75: Comprobar que las dependencias están instaladas y configuradas
- Día 76-77: Confirmar que los servicios están correctamente configurados
- Día 78: Revisión final de consistencia de nombres y eliminación de recursos sin usar
- Día 79-80: Corrección automática de inconsistencias detectadas
- Día 81: Generación del archivo flag.txt con las four flags
- Día 82: Revisión final y preparación para entrega

Total estimado: 11-12 semanas de trabajo dedicado