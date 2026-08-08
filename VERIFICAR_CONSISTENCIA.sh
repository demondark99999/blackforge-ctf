#!/bin/bash
# Script de verificación de consistencia para BlackForge Labs Instalación Omega
# Fase 6: Auditoría - Verificación automática de consistencia del proyecto

set -e

echo "Iniciando verificación de consistencia del proyecto BlackForge Labs..."
echo "============================================================"

ERROR_COUNT=0
WARNING_COUNT=0

# Función para reportar errores
report_error() {
    echo "[ERROR] $1"
    ((ERROR_COUNT++))
}

# Función para reportar advertencias
report_warning() {
    echo "[WARNING] $1"
    ((WARNING_COUNT++))
}

# Función para reportar información
report_info() {
    echo "[INFO] $1"
}

# Verificar que los directorios principales existan
report_info "Verificando estructura de directorios principal..."
for dir in ENTORNO_PRINCIPAL ENTORNO_SECUNDARIO DOCUMENTACION; do
    if [ ! -d "$dir" ]; then
        report_error "Directorio principal no encontrado: $dir"
    else
        report_info "Directorio encontrado: $dir"
    fi
done

# Verificar docker-compose del entorno principal
report_info "Verificando docker-compose principal..."
if [ ! -f "ENTORNO_PRINCIPAL/docker-compose.yml" ]; then
    report_error "Archivo docker-compose.yml no encontrado en ENTORNO_PRINCIPAL"
else
    report_info "Archivo docker-compose.yml encontrado en ENTORNO_PRINCIPAL"
    # Verificar que los servicios referenciados existen
    while IFS= read -r line; do
        if [[ $line =~ ^[[:space:]]*[a-zA-Z0-9_-]+:[[:space:]]*$ ]]; then
            service_name=$(echo "$line" | sed 's/[[:space:]]*$//' | sed 's/:$//')
            service_dir="ENTORNO_PRINCIPAL/servicios/$service_name"
            if [ ! -d "$service_dir" ] && [ ! -f "ENTORNO_PRINCIPAL/infra/$service_name/Dockerfile" ] && [ ! -f "ENTORNO_PRINCIPAL/servicios/$service_name/Dockerfile" ]; then
                # Algunos servicios pueden estar en infra/
                if [ "$service_name" = "nginx-proxy" ]; then
                    service_dir="ENTORNO_PRINCIPAL/infra/nginx"
                elif [ "$service_name" = "database" ]; then
                    service_dir="ENTORNO_PRINCIPAL/servicios/db"
                elif [ "$service_name" = "cache" ]; then
                    service_dir="ENTORNO_PRINCIPAL/servicios/cache"
                elif [ "$service_name" = "mail" ]; then
                    service_dir="ENTORNO_PRINCIPAL/servicios/mail"
                elif [ "$service_name" = "ftp-server" ]; then
                    service_dir="ENTORNO_PRINCIPAL/servicios/ftp"
                fi

                if [ ! -d "$service_dir" ]; then
                    report_warning "Directorio para servicio '$service_name' no encontrado claramente (puede ser résolution implícita)"
                fi
            fi
        fi
    done < <(grep -E '^[[:space:]]*[a-zA-Z0-9_-]+:[[:space:]]*$' ENTORNO_PRINCIPAL/docker-compose.yml)
fi

# Verificar docker-compose del entorno secundario
report_info "Verificando docker-compose secundario..."
if [ ! -f "ENTORNO_SECUNDARIO/docker-compose.yml" ]; then
    report_error "Archivo docker-compose.yml no encontrado en ENTORNO_SECUNDARIO"
else
    report_info "Archivo docker-compose.yml encontrado en ENTORNO_SECUNDARIO"
fi

# Verificar existencia de Dockerfiles referenciados
report_info "Verificando existencia de Dockerfiles..."

# Verificar Dockerfiles en ENTORNO_PRINCIPAL/servicios
for dockerfile in ENTORNO_PRINCIPAL/servicios/*/Dockerfile; do
    if [ -f "$dockerfile" ]; then
        service_name=$(basename $(dirname "$dockerfile"))
        report_info "Dockerfile encontrado para servicio principal: $service_name"
    fi
done

# Verificar Dockerfiles en ENTORNO_PRINCIPAL/infra
for dockerfile in ENTORNO_PRINCIPAL/infra/*/Dockerfile; do
    if [ -f "$dockerfile" ]; then
        service_name=$(basename $(dirname "$dockerfile"))
        report_info "Dockerfile encontrado para infraestructura: $service_name"
    fi
done

# Verificar Dockerfiles en ENTORNO_SECUNDARIO/servicios
for dockerfile in ENTORNO_SECUNDARIO/servicios/*/*/Dockerfile ENTORNO_SECUNDARIO/servicios/*/Dockerfile; do
    if [ -f "$dockerfile" ]; then
        service_path=$(dirname "$dockerfile")
        service_name=$(basename "$service_path")
        report_info "Dockerfile encontrado para servicio secundario: $service_name"
    fi
done

# Verificar scripts referenciados en Dockerfiles
report_info "Verificando referencias a scripts en Dockerfiles..."

# Verificar en ENTORNO_PRINCIPAL
for dockerfile in ENTORNO_PRINCIPAL/servicios/*/Dockerfile ENTORNO_PRINCIPAL/infra/*/Dockerfile; do
    if [ -f "$dockerfile" ]; then
        service_name=$(basename $(dirname "$dockerfile"))
        # Buscar referencias a scripts COPY o ADD
        while IFS= read -r line; do
            if [[ $line =~ COPY[[:space:]]+(.*)[[:space:]]+.*\.sh ]] || [[ $line =~ ADD[[:space:]]+(.*)[[:space:]]+.*\.sh ]]; then
                script_ref=$(echo "$line" | sed -E 's/.*(COPY|ADD)[[:space:]]+([^[:space:]]+\.sh)[[:space:]]+.*/\2/' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
                if [ -n "$script_ref" ]; then
                    # Determinar el directorio base
                    base_dir=$(dirname "$dockerfile")
                    script_path="$base_dir/$script_ref"
                    if [ ! -f "$script_path" ]; then
                        report_warning "Script referenciado en $dockerfile no encontrado: $script_ref"
                    else
                        report_info "Script encontrado: $script_ref (en $service_name)"
                    fi
                fi
            fi
        done < "$dockerfile"
    fi
done

# Verificar en ENTORNO_SECUNDARIO
for dockerfile in ENTORNO_SECUNDARIO/servicios/*/*/Dockerfile ENTORNO_SECUNDARIO/servicios/*/Dockerfile; do
    if [ -f "$dockerfile" ]; then
        service_path=$(dirname "$dockerfile")
        service_name=$(basename "$service_path")
        # Buscar referencias a scripts COPY o ADD
        while IFS= read -r line; do
            if [[ $line =~ COPY[[:space:]]+(.*)[[:space:]]+.*\.sh ]] || [[ $line =~ ADD[[:space:]]+(.*)[[:space:]]+.*\.sh ]]; then
                script_ref=$(echo "$line" | sed -E 's/.*(COPY|ADD)[[:space:]]+([^[:space:]]+\.sh)[[:space:]]+.*/\2/' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
                if [ -n "$script_ref" ]; then
                    # Determinar el directorio base
                    base_dir="$service_path"
                    script_path="$base_dir/$script_ref"
                    if [ ! -f "$script_path" ]; then
                        report_warning "Script referenciado en $dockerfile no encontrado: $script_ref"
                    else
                        report_info "Script encontrado: $script_ref (en $service_name)"
                    fi
                fi
            fi
        done < "$dockerfile"
    fi
done

# Verificar existencia de archivos de configuración referenciados
report_info "Verificando archivos de configuración referenciados..."
# Esta verificación sería más compleja, pero al menos verificamos algunos obvios

# Verificar archivos .env
if [ ! -f "ENTORNO_PRINCIPAL/.env" ]; then
    report_warning "Archivo .env no encontrado en ENTORNO_PRINCIPAL (se esperaba .env.example como plantilla)"
else
    report_info "Archivo .env encontrado en ENTORNO_PRINCIPAL"
fi

if [ ! -f "ENTORNO_SECUNDARIO/.env" ]; then
    report_warning "Archivo .env no encontrado en ENTORNO_SECUNDARIO (se esperaba .env.example como plantilla)"
else
    report_info "Archivo .env encontrado en ENTORNO_SECUNDARIO"
fi

# Verificar existencia de documentación clave
report_info "Verificando documentación clave..."
for doc in DOCUMENTACION/ARQUITECTURA.md DOCUMENTACION/HISTORIA_LABORATORIO.md DOCUMENTACION/GUIA_USUARIO.md DOCUMENTACION/API_REFERENCE.md; do
    if [ ! -f "$doc" ]; then
        report_error "Documento clave no encontrado: $doc"
    else
        report_info "Documento encontrado: $doc"
    fi
done

# Verificar existencia de archivos legales
report_info "Verificando archivos legales y de gestión..."
for legal in LICENSE README.md CONTRIBUTING.md CODE_OF_CONDUCT.md SECURITY.md CHANGELOG.md; do
    if [ ! -f "$legal" ]; then
        report_warning "Archivo legal/gestión no encontrado: $legal"
    else
        report_info "Archivo encontrado: $legal"
    fi
done

# Verificar existencia de .gitignore
report_info "Verificando archivos de gestión de repositorio..."
if [ ! -f ".gitignore" ]; then
    report_warning "Archivo .gitignore no encontrado"
else
    report_info "Archivo .gitignore encontrado"
fi

# Verificar que el repositorio git esté inicializado
if [ ! -d ".git" ]; then
    report_error "Repositorio git no inicializado"
else
    report_info "Repositorio git inicializado encontrado"
fi

# Resumen
echo ""
echo "============================================================"
echo "RESUMEN DE VERIFICACIÓN:"
echo "  Errores encontrados: $ERROR_COUNT"
echo "  Advertencias encontradas: $WARNING_COUNT"

if [ $ERROR_COUNT -eq 0 ]; then
    echo ""
    echo "��✅ VERIFICACIÓN COMPLETADA - No se encontraron errores críticos"
    echo "   El proyecto parece estar en un estado consistente"
else
    echo ""
    echo "��❌ VERIFICACIÓN COMPLETADA - Se encontraron $ERROR_COUNT errores que requieren atención"
    echo "   Por favor revise los errores listados arriba"
fi

if [ $WARNING_COUNT -gt 0 ]; then
    echo ""
    echo "��⚠��️  Se encontraron $WARNING_COUNT advertencias que podrían requerir atención"
fi

echo ""
echo "Para corregir los problemas encontrados, por favor:"
echo "  1. Revise cada mensaje de error y advertencia"
echo "  2. Cree los archivos o directorios faltantes según sea necesario"
echo "  3. Corrija las referencias incorrectas en los archivos de configuración"
echo "  4. Ejecute este script de nuevo para verificar que los problemas se resolvieron"

# Salir con código de error si se encontraron errores
if [ $ERROR_COUNT -gt 0 ]; then
    exit 1
else
    exit 0
fi