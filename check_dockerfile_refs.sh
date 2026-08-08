#!/bin/bash
# Script rápido para verificar referencias en Dockerfiles

echo "Verificando referencias en Dockerfiles..."
echo "======================================"

MISSING_COUNT=0

# Función para verificar referencias en un Dockerfile
check_dockerfile() {
    local dockerfile="$1"
    local service_name="$2"
    local base_dir="$3"

    if [ -f "$dockerfile" ]; then
        # Buscar referencias a archivos COPY o ADD
        while IFS= read -r line; do
            if [[ $line =~ COPY[[:space:]]+(.*)[[:space:]]+ ]] || [[ $line =~ ADD[[:space:]]+(.*)[[:space:]]+ ]]; then
                # Extraer la referencia (primer argumento después de COPY/ADD)
                ref=$(echo "$line" | sed -E 's/.*(COPY|ADD)[[:space:]]+([^[:space:]]+)[[:space:]]+.*/\2/' | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
                if [ -n "$ref" ] && [[ ! "$ref" =~ ^--from= ]] && [[ ! "$ref" =~ \. ]] && [[ ! "$ref" =~ ^\./ ]] && [[ ! "$ref" =~ ^\/ ]]; then
                    # Esto parece ser una ruta relativa simple
                    file_path="$base_dir/$ref"
                    if [ ! -f "$file_path" ]; then
                        echo "[ERROR] Archivo referenciado en $dockerfile no encontrado: $ref"
                        ((MISSING_COUNT++))
                    else
                        echo "[OK] Archivo encontrado: $ref (en $service_name)"
                    fi
                fi
            fi
        done < "$dockerfile"
    fi
}

# Verificar en ENTORNO_PRINCIPAL
echo "Revisando ENTORNO_PRINCIPAL..."
for dockerfile in ENTORNO_PRINCIPAL/servicios/*/Dockerfile ENTORNO_PRINCIPAL/infra/*/Dockerfile; do
    if [ -f "$dockerfile" ]; then
        service_name=$(basename $(dirname "$dockerfile"))
        base_dir=$(dirname "$dockerfile")
        check_dockerfile "$dockerfile" "$service_name" "$base_dir"
    fi
done

# Verificar en ENTORNO_SECUNDARIO
echo ""
echo "Revisando ENTORNO_SECUNDARIO..."
for dockerfile in ENTORNO_SECUNDARIO/servicios/*/*/Dockerfile ENTORNO_SECUNDARIO/servicios/*/Dockerfile; do
    if [ -f "$dockerfile" ]; then
        service_path=$(dirname "$dockerfile")
        service_name=$(basename "$service_path")
        check_dockerfile "$dockerfile" "$service_name" "$service_path"
    fi
done

echo ""
echo "Verificación completada."
echo "Archivos faltantes encontrados: $MISSING_COUNT"

if [ $MISSING_COUNT -eq 0 ]; then
    echo "��✅ No se encontraron referencias faltantes en Dockerfiles"
else
    echo "��❌ Se encontraron $MISSING_COUNT referencias faltantes que necesitan atención"
fi

exit $MISSING_COUNT