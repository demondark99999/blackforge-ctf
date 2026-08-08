FROM python:3.11-slim

# Instalar Ansible y dependencias
RUN pip install --no-cache-dir ansible==8.2.0 \
    && apt-get update && apt-get install -y \
    ssh \
    rsync \
    && rm -rf /var/lib/apt/lists/*

# Crear directorios necesarios
RUN mkdir -p /etc/ansible /opt/playbooks /etc/ansible/hosts /var/log/ansible

# Copiar configuración y playbooks de ejemplo
COPY config/ansible.cfg /etc/ansible/ansible.cfg
COPY inventory/ /etc/ansible/
COPY playbooks/ /opt/playbooks/

# Exponer ningún puerto (servicio de automatización internal)
# Pero si se necesita una API o interfaz web, se podría añadir aquí

# Volumen para configuración, playbooks y logs
VOLUME ["/etc/ansible", "/opt/playbooks", "/var/log/ansible"]

# Comando de entrada (se puede sobrescribir con comandos específicos)
CMD ["ansible-playbook", "--version"]