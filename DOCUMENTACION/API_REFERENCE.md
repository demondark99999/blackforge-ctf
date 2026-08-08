# Referencia de API - BlackForge Labs Instalación Omega

## Visión General

Esta documento detalla los endpoints de la API REST disponible en BlackForge Labs Instalación Omega. La API está diseñada para permitir la interacción programática con diversos componentes del sistema, facilitando tareas de automatización, integración y desarrollo de herramientas personalizadas para el entrenamiento en ciberseguridad.

**Nota de Seguridad**: Algunos endpoints contienen vulnerabilidades intencionales para fines de entrenamiento CTF. Estos están claramente marcados y NO deben replicarse en entornos de producción.

## Autenticación

La API soporta dos métodos de autenticación:

### 1. Autenticación por Sesión (Predeterminado)
- Utiliza las mismas cookies de sesión que la interfaz web
- Se obtiene al iniciar sesión mediante `/login`
- Debe incluir la cookie de sesión en las solicitudes

### 2. Autenticación por Token Bearer
- Incluir el encabezado: `Authorization: Bearer <token>`
- Tokens válidos se obtienen mediante elendpoint de autenticación o se proporcionan en el contexto del ejercicio
- Para este entorno, un token de demostración es: `demo-api-token-for-training-only`

### Encabezados Comunes
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer <token>  # Opcional si se usa autenticación por sesión
```

## Manejo de Errores

La APIレスponses con códigos de estado HTTP estándar y un formato JSON consistente para errores:

```json
{
  "success": false,
  "error": "Descripción del error",
  "details": {
    // Información adicional opcional según el tipo de error
  }
}
```

Respuestas exitosas:
```json
{
  "success": true,
  // Datos específicos del endpoint
}
```

## Rate Limiting

Para prevenir abusos y asegurar la disponibilidad del servicio, se aplican límites de velocidad:
- **Anónimo**: 10 solicitudes por minuto
- **Autenticado**: 60 solicitudes por minuto
- **Endpoints críticos** (como autenticación): 5 solicitudes por minuto

Cuando se excede el límite, se retorna:
- Código de estado: `429 Too Many Requests`
- Encabezado: `Retry-After: <segundos>`

## Endpoints de la API

### Autenticación

#### POST `/api/auth/login`
Inicia sesión y retorna información del usuario

**Parámetros:**
```json
{
  "username": "string",
  "password": "string"
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "username": "ejemplo",
    "email": "usuario@ejemplo.com",
    "full_name": "Usuario Ejemplo",
    "role": "user",
    "token": "jwt-token-o-session-id"
  }
}
```

#### POST `/api/auth/logout`
Cierra la sesión actual

**Respuesta Exitosa:**
```json
{
  "success": true,
  "message": "Sesión cerrada exitosamente"
}
```

#### GET `/api/auth/me`
Obtiene información del usuario autenticado actual

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "username": "ejemplo",
    "email": "usuario@ejemplo.com",
    "full_name": "Usuario Ejemplo",
    "role": "user",
    "last_login": "2024-08-07T10:30:00Z",
    "created_at": "2024-01-15T08:00:00Z"
  }
}
```

### Gestión de Usuarios

#### GET `/api/users`
Lista usuarios (requiere rol admin o operador)

**Parámetros de Consulta:**
- `limit` (int, default: 20): Número máximo de resultados
- `offset` (int, default: 0): Número de resultados a omitir
- `search` (string, optional): Filtrar por nombre de usuario, email o nombre completo
- `role` (string, optional): Filtrar por rol específico

**Respuesta Exitosa:**
```json
{
  "success": true,
  "count": 45,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "email": "admin@forgelabs.local",
      "full_name": "Administrador del Sistema",
      "role": "admin",
      "is_active": true,
      "created_at": "2024-01-10T08:00:00Z",
      "last_login": "2024-08-07T09:15:00Z"
    }
    // ... más usuarios
  ]
}
```

#### GET `/api/users/{id}`
Obtiene detalles de un usuario específico

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "username": "ejemplo",
    "email": "usuario@ejemplo.com",
    "full_name": "Usuario Ejemplo",
    "role": "user",
    "is_active": true,
    "created_at": "2024-05-15T14:22:00Z",
    "last_login": "2024-08-07T10:30:00Z",
    "failed_login_attempts": 0,
    "profile": {
      "avatar_url": null,
      "bio": "Usuario de entrenamiento estándar",
      "department": null,
      "position": null
    }
  }
}
```

#### POST `/api/users`
Crea un nuevo usuario (requiere rol admin)

**Parámetros:**
```json
{
  "username": "string",
  "email": "string",
  "password": "string",
  "full_name": "string optional",
  "role": "string optional (default: user)"
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 124,
    "username": "nuevousuario",
    "email": "nuevo@ejemplo.com",
    "full_name": "Nuevo Usuario",
    "role": "user",
    "created_at": "2024-08-07T11:00:00Z"
  }
}
```

#### PUT `/api/users/{id}`
Actualiza un usuario existente (requiere rol admin o propio usuario con limitaciones)

**Parámetros:**
```json
{
  "full_name": "string optional",
  "email": "string optional",
  "password": "string optional"
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "username": "ejemplo",
    "email": "nuevoemail@ejemplo.com",
    "full_name": "Nombre Actualizado",
    "role": "user",
    "updated_at": "2024-08-07T11:30:00Z"
  }
}
```

#### DELETE `/api/users/{id}`
Elimina un usuario (requiere rol admin)

**Respuesta Exitosa:**
```json
{
  "success": true,
  "message": "Usuario eliminado exitosamente"
}
```

### Gestión de Archivos

#### GET `/api/files`
Lista archivos subidos por el usuario autenticado

**Parámetros de Consulta:**
- `limit` (int, default: 20)
- `offset` (int, default: 0)
- `search` (string, optional): Filtrar por nombre original
- `file_type` (string, optional): Filtrar por extensión (jpg, pdf, etc.)
- `is_public` (boolean, optional): Filtrar por visibilidad

**Respuesta Exitosa:**
```json
{
  "success": true,
  "count": 15,
  "data": [
    {
      "id": 45,
      "original_name": "documento.pdf",
      "file_size": 245760,
      "mime_type": "application/pdf",
      "upload_path": "/uploads/abc123def456.pdf",
      "upload_date": "2024-08-06T14:22:00Z",
      "is_public": false,
      "scanned": true
    }
    // ... más archivos
  ]
}
```

#### GET `/api/files/{id}`
Obtiene detalles de un archivo específico

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 45,
    "original_name": "documento.pdf",
    "file_size": 245760,
    "mime_type": "application/pdf",
    "upload_path": "/uploads/abc123def456.pdf",
    "upload_date": "2024-08-06T14:22:00Z",
    "is_public": false,
    "scanned": true,
    "uploader": {
      "id": 123,
      "username": "ejemplo"
    }
  }
}
```

#### POST `/api/files`
Sube un nuevo archivo

**Nota**: Este endpoint espera datos multipart/form-data, no JSON

**Parámetros (FormData):**
- `file`: El archivo a subir
- `description` (string, optional): Descripción del archivo

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 46,
    "original_name": "nuevo_archivo.txt",
    "file_size": 1024,
    "mime_type": "text/plain",
    "upload_path": "/uploads/def789ghi012.txt",
    "upload_date": "2024-08-07T11:45:00Z",
    "is_public": false,
    "scanned": false
  }
}
```

#### DELETE `/api/files/{id}`
Elimina un archivo específico

**Respuesta Exitosa:**
```json
{
  "success": true,
  "message": "Archivo eliminado exitosamente"
}
```

### Gestión de Comentarios

#### GET `/api/comments`
Lista comentarios (con filtros opcionales)

**Parámetros de Consulta:**
- `limit` (int, default: 20)
- `offset` (int, default: 0)
- `search` (string, optional): Filtrar por contenido
- `user_id` (int, optional): Filtrar por ID de usuario
- `sort` (string, default: "created_at_desc"): Orden de resultados

**Respuesta Exitosa:**
```json
{
  "success": true,
  "count": 8,
  "data": [
    {
      "id": 12,
      "content": "Este es un comentario de ejemplo",
      "created_at": "2024-08-05T16:30:00Z",
      "user": {
        "id": 123,
        "username": "ejemplo"
      }
    }
    // ... más comentarios
  ]
}
```

#### POST `/api/comments`
Crea un nuevo comentario

**Parámetros:**
```json
{
  "content": "string",
  "parent_id": "int optional (for replies)"
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 13,
    "content": "Nuevo comentario creado",
    "created_at": "2024-08-07T12:00:00Z",
    "user": {
      "id": 123,
      "username": "ejemplo"
    }
  }
}
```

### Estadísticas y Información del Sistema

#### GET `/api/stats`
Obtiene estadísticas generales del sistema

**Nota de Seguridad**: Este endpoint contiene información potencialmente sensible y está intencionalmente expuesto para ejercicios de reconocimiento de información.

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "users": {
      "total": 124,
      "active": 98,
      "admins": 5,
      "operators": 12
    },
    "files": {
      "total": 234,
      "total_size_bytes": 15728640,
      "uploads_today": 12
    },
    "comments": {
      "total": 89,
      "today": 7
    },
    "system": {
      "uptime_seconds": 345600,
      "load_average": [0.45, 0.32, 0.28],
      "memory_usage": {
        "total_mb": 8192,
        "used_mb": 4096,
        "percentage": 50
      },
      "disk_usage": {
        "total_gb": 100,
        "used_gb": 45,
        "percentage": 45
      },
      "php_version": "8.2.8",
      "mysql_version": "8.0.34",
      "redis_version": "7.0.12"
    },
    "security": {
      "failed_logins_today": 23,
      "blocked_ips": 3,
      "suspicious_activities": 5,
      "last_security_scan": "2024-08-06T02:00:00Z"
    }
  }
}
```

#### GET `/api/health`
Verifica el estado de salud de los servicios

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "timestamp": "2024-08-07T12:00:00Z",
    "services": {
      "web-app": {
        "status": "healthy",
        "response_time_ms": 45,
        "version": "1.2.0"
      },
      "database": {
        "status": "healthy",
        "response_time_ms": 12,
        "version": "8.0.34"
      },
      "redis": {
        "status": "healthy",
        "response_time_ms": 8,
        "version": "7.0.12"
      },
      "mail": {
        "status": "healthy",
        "response_time_ms": 15,
        "version": "3.7.2"
      },
      "ftp": {
        "status": "healthy",
        "response_time_ms": 10,
        "version": "3.1.3"
      }
    }
  }
}
```

### Endpoints de Entrenamiento (Con Vulnerabilidades Intencionales)

#### �� ⚠��️ API: Ejecutar Consulta Arbitraria (INYECCIÓN SQL INTENCIONAL)
**ADVERTENCIA**: Este endpoint está diseñado con una vulnerabilidad de inyección SQL intencional para fines de entrenamiento. NO usar este patrón en aplicaciones reales.

#### GET `/api/query`
Ejecuta una consulta SQL arbitraria (MUY PELIGROSO - SOLO PARA ENTRENAMIENTO)

**Parámetros de Consulta:**
- `sql` (string, required): Consulta SQL a ejecutar

**Respuesta Exitosa (para SELECT):**
```json
{
  "success": true,
  "row_count": 5,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "email": "admin@forgelabs.local"
    }
    // ... más filas
  ]
}
```

**Respuesta Exitosa (para modificativas):**
```json
{
  "success": true,
  "affected_rows": 3,
  "message": "Consulta ejecutada exitosamente"
}
```

#### �� ⚠��️ API: Ejecutar Comando Arbitrario (INYECCIÓN DE COMANDO INTENCIONAL)
**ADVERTENCIA**: Este endpoint está diseñado con una vulnerabilidad de inyección de comando intencional para fines de entrenamiento. NO usar este patrón en aplicaciones reales.

#### POST `/api/execute`
Ejecuta un comando arbitrario en el servidor (MUY PELIGROSO - SOLO PARA ENTRENAMIENTO)

**Parámetros:**
```json
{
  "command": "string",
  "timeout": "int optional (default: 30)"
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "output": "resultado del comando aquí",
  "exit_code": 0,
  "execution_time_ms": 125
}
```

#### �� ⚠��️ API: Acceso a Información Sensible (EXPOSICIÓN INTENCIONAL)
**ADVERTENCIA**: Este endpoint expone información que debería estar protegida en una aplicación real.

#### GET `/api/debug/info`
Obtiene información de depuración del sistema

**Respuesta Exitosa:**
```json
{
  "success": true,
  "data": {
    "environment": [
      "APP_ENV=production",
      "DEBUG=false",
      "LOG_LEVEL=warning"
    ],
    "included_files": [
      "/var/www/html/index.php",
      "/var/www/html/config/database.php",
      "/var/www/html/config/security.php"
    ],
    "server_variables": {
      "DOCUMENT_ROOT": "/var/www/html",
      "SERVER_NAME": "localhost",
      "SERVER_PORT": "80",
      "REQUEST_URI": "/api/debug/info"
    },
    "php_info": {
      "version": "8.2.8",
      "api_no": "20230831",
      "zend_debug_build": false,
      "thread_safe": false
    },
    "extensions_loaded": [
      "Core", "date", "libxml", "openssl", "pcre", "sqlite3", "zlib",
      "bcmath", "calendar", "ctype", "curl", "dom", "hash", "fileinfo",
      "filter", "ftp", "gd", "gettext", "iconv", "json", "mbstring",
      "mysqli", "pdo_mysql", "PDO", "session", "SimpleXML", "soap",
      "sockets", "sodium", "SPL", "standard", "tokenizer", "xml",
      "xmlreader", "xmlreader", "zip"
    ]
  }
}
```

## WebSocket API (Funcionalidad Experimental)

> **Nota**: La siguiente sección describe una función experimental que puede no estar disponible en todas las instancias.

### Conexión WebSocket
```
ws://localhost:8080/ws
```

#### Eventos del Cliente
| Evento | Descripción | Datos |
|--------|-------------|-------|
| `join` | Unirse a una sala de entrenamiento | `{ room: string, user_id: int }` |
| `leave` | Abandonar una sala de entrenamiento | `{ room: string }` |
| `message` | Enviar mensaje a la sala | `{ room: string, content: string }` |
| `command` | Ejecutar comando de entrenamiento | `{ action: string, params: object }` |

#### Eventos del Servidor
| Evento | Descripción | Datos |
|--------|-------------|-------|
| `connected` | Conexión establecida exitosamente | `{ user_id: int, username: string, rooms: string[] }` |
| `disconnected` | Desconexión del servidor | `{ reason: string }` |
| `user_joined` | Usuario se unió a la sala | `{ user_id: int, username: string, room: string }` |
| `user_left` | Usuario abandonó la sala | `{ user_id: int, username: string, room: string }` |
| `message_recived` | Mensaje recibido en la sala | `{ user_id: int, username: string, room: string, content: string, timestamp: string }` |
| `command_result` | Resultado de ejecución de comando | `{ action: string, success: bool, data: object, timestamp: string }` |
| `system_alert` | Alerta del sistema | `{ level: string, message: string, timestamp: string }` |
| `training_event` | Evento específico del entrenamiento | `{ type: string, data: object, timestamp: string }` |

## Buenas Prácticas para el Uso de la API

### Seguridad
1. **Nunca almacene credenciales en código fuente**: Use variables de entorno o almacenes seguros
2. **Valide siempre las entradas**: Incluso si provienen de fuentes "de confianza"
3. **Implemente límite de velocidad en su lado**: No dependa únicamente del límite del servidor
4. **Use HTTPS en entornos reales**: Aunque este entorno utiliza HTTP para facilitar el entrenamiento
5. **Rotar tokens y credenciales regularmente**: Especialmente en entornos de larga duración
6. **Audite el uso de la API**: Mantenga logs de quién accede a qué y cuándo

### Rendimiento
1. **Agrupe solicitudes cuando sea posible**: Reduce el número de llamadas HTTP
2. **Use paginación para listas grandes**: No solicite más datos de los necesarios
3. **Implemente caching apropiado**: Almacene resultados que no cambian frecuentemente
4. **Compres respuestas grandes**: Si la API lo soporta, use codificación gzip
5. **Evite consultas N+1**: Optimice sus patrones de acceso a datos
6. **Use conexiones persistentes**: Cuando sea apropiado para reducir overhead

### Manejo de Errores
1. **Verifique siempre el código de estado HTTP**: No asuma que todas las respuestas son exitosas
2. **Maneje los errores de ratelimit adecuadamente**: Respete el encabezado Retry-After
3. **Implemente reintentos con retrocedo exponencial**: Para errores transitorios
4. **Proporcione retroalimentación útil al usuario**: No muestre errores técnicos crudos
5. **Registre errores para depuración**: Pero no exponga información sensible a usuarios finales
6. **Tenha un plan de contingencia**: ¿Qué hacer si la API no está disponible?

### Testing y Desarrollo
1. **Use entornos de staging**: Nunca pruebe directamente contra producción si es posible evitarlo
2. **Automatice sus pruebas**: Incluso scripts simples pueden prevenir regresiones
3. **Pruebe casos edge**: ¿Qué pasa con entradas vacías, muy grandes o en formatos inesperados?
4. **Valide los tipos de datos**: No asuma que la API siempre retornará lo que espera
5. **Maneje la paginación correctamente**: Especialmente al final de listas
6. **Desarrolle contra la especificación, no contra la implementación**: Esto hará su código más resistente a cambios

## Códigos de Estado HTTP Específicos Utilizados

| Código | Nombre | Uso Específico en Esta API |
|--------|--------|----------------------------|
| 200 | OK | Solicitud exitosa estándar |
| 201 | Created | Recurso creado exitosamente (POST/PUT que crea) |
| 204 | No Content | Acción exitosa pero no hay datos que retornar (DELETE) |
| 400 | Bad Request | Parámetros faltantes o inválidos, formato incorrecto |
| 401 | Unauthorized | Falta de autenticación o token inválido |
| 403 | Forbidden | Autenticado pero sin permisos para el recurso solicitado |
| 404 | Not Found | Recurso no encontrado o endpoint no existe |
| 405 | Method Not Allowed | Método HTTP no permitido para ese endpoint |
| 409 | Conflict | Conflicto con estado actual (ej. intentar crear usuario existente) |
| 422 | Unprocessable Entity | Datos sintácticamente correctos pero semánticamente inválidos |
| 429 | Too Many Requests | Límite de velocidad excedido |
| 500 | Internal Server Error | Error inesperado en el servidor interno |
| 502 | Bad Gateway | Error de comunicación como proxy o pasarela |
| 503 | Service Unavailable | Servicio temporalmente no disponible |
| 504 | Gateway Timeout | Tiempo de espera agotado esperando servicio upstream |

## Versionado de la API

Esta documentación corresponde a la versión **v1.0.0** de la API.

### Política de Versionado
- **Versiones Mayores** (x.0.0): Cambios que rompen compatibilidad hacia atrás
- **Versiones Menores** (x.y.0): Nuevas funcionalidades compatibles hacia atrás
- **Versiones de Parche** (x.y.z): Correcciones de errores y mejoras menores

### Encabezados de Versionado
La API soporta versionado mediante encabezados:
```
Accept: application/vnd.blackforge.v1+json
```

O mediante prefijo en la URL:
```
/api/v1/users
```

## Ejemplos de Uso

### Ejemplo 1: Iniciar Sesión y Obtener Datos de Usuario (bash + curl)
```bash
# Iniciar sesión
LOGIN_RESPONSE=$(curl -s -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"AdminSecurePass!2024"}')

# Extraer token
TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

# Obtener información del usuario
curl -s -X GET http://localhost/api/auth/me \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

### Ejemplo 2: Subir un Archivo (Python + requests)
```python
import requests

# Iniciar sesión primero
login_data = {"username": "user01", "password": "Password123!"}
login_resp = requests.post("http://localhost/api/auth/login", json=login_data)
token = login_resp.json()["data"]["token"]

# Subir archivo
files = {"file": ("documento.txt", open("documento.txt", "rb"), "text/plain")}
headers = {"Authorization": f"Bearer {token}"}
upload_resp = requests.post(
    "http://localhost/api/files",
    files=files,
    headers=headers
)

print(upload_resp.json())
```

### Ejemplo 3: Consultar Estadísticas del Sistema (JavaScript + Fetch)
```javascript
async function getSystemStats() {
  try {
    const response = await fetch('http://localhost/api/stats', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    return data.data;
  } catch (error) {
    console.error('Error fetching system stats:', error);
    throw error;
  }
}

// Uso
getSystemStats().then(stats => {
  console.log('Estadísticas del Sistema:', stats);
}).catch(err => {
  console.error('Failed to get stats:', err);
});
```

### Ejemplo 4: Demostrando Vulnerabilidad de Inyección SQL (Para Entrenamiento Solo)
> **ADVERTENCIA**: Este ejemplo demuestra una vulnerabilidad intencional. NO usar en producción.

```bash
# Consulta para obtener todos los usuarios (INYECCIÓN SQL)
curl -s "http://localhost/api/query?sql=SELECT%20id,username,email,role%20FROM%20users"

# Consulta para extraer información de la base de datos
curl -s "http://localhost/api/query?sql=SELECT%20@@version,%20user()"
```

### Ejemplo 5: Acceso a Información de Depuración (Para Entrenamiento Solo)
> **ADVERTENCIA**: Este endpoint expone información sensible intencionalmente.

```bash
# Obtener información de depuración del sistema
curl -s "http://localhost/api/debug/info"
```

## Glosario de Términos de API

- **Endpoint**: Una URL específica que representa una función o recurso en la API
- **HTTP Verb**: Método de HTTP utilizado (GET, POST, PUT, DELETE, PATCH, etc.)
- **Payload**: Los datos enviados en el cuerpo de una solicitud (usually JSON or form data)
- **Response**: La datos retornados por el servidor en respuesta a una solicitud
- **Status Code**: Código numérico que indica el resultado de una solicitud HTTP
- **Headers**: Información adicional enviada junto con solicitudes y respuestas HTTP
- **Query String**: Parte de la URL que contiene parámetros después del signo de interrogación (?)
- **Rate Limiting**: Mecanismo que limita el número de solicitudes que un cliente puede hacer en un período de tiempo
- **Idempotent**: Propiedad de una operación donde múltiples aplicaciones idénticas tienen el mismo efecto que una sola aplicación
- **REST**: Representational State Transfer - estilo arquitectónico para APIs de red
- **CRUD**: Create, Read, Update, Update - operaciones básicas de almacenamiento persistente
- **HATEOAS**: Hypermedia As The Engine Of Application State - principio de REST que incluye enlaces en respuestas
- **JSONP**: JSON with Padding - técnica para superar restricciones de mismo origen en navegadores antiguos
- **GraphQL**: Lenguaje de consulta para APIs y tiempo de ejecución para cumplir esas consultas con datos existentes
- **gRPC**: Marco de llamada a procedimiento remoto (RPC) de alto rendimiento de código abierto
- **OpenAPI**: Especificación para definir APIs REST (anteriormente conocido como Swagger)
- **Postman**: Herramienta popular para probar y documentar APIs
- **cURL**: Herramienta de línea de comando para transferir datos usando varios protocolos
- **HTTPie**: Cliente de línea de comando amigable para hacer solicitudes HTTP
- **REST-assured**: Biblioteca de Java para probar servicios REST
- **Karate**: Framework de código abierto para probar servicios de API basado en Cucumber

## Soporte y Actualizaciones

Para obtener la versión más reciente de esta documentación o reportar problemas:
- Revise el archivo `API_REFERENCE.md` en el repositorio principal
- Consulte el `CHANGELOG.md` para historial de cambios
- Reportar problemas a través de los canales de soporte designados
- Sugiera mejoras mediante pull requests o issues en el repositorio de desarrollo

*Esta documentación se genera automáticamente como parte del proceso de despliegue y refleja el estado actual de la API en el entorno BlackForge Labs Instalación Omega.*

---
*Última actualización: Agosto 2024*
*Versión de la API: v1.0.0*
*BlackForge Labs - Todos los derechos reservados*