// JavaScript principal para BlackForge Labs - Instalación Omega
// Funcionalidades frontend para el entorno de entrenamiento CTF

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips (simulado)
    initTooltips();

    // Inicializar menú móvil
    initMobileMenu();

    // Inicializar alertas auto-ocultables
    initAutoDismissAlerts();

    // Inicializar validación de formularios
    initFormValidation();

    // Inicializar carga diferida de imágenes (simulado)
    initLazyLoading();

    // Inicializar contador regresivo para sesiones (simulado)
    initSessionCountdown();
});

/**
 * Inicializar tooltips simples basado en atributo title
 */
function initTooltips() {
    const elements = document.querySelectorAll('[title]');
    elements.forEach(el => {
        el.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.style.position = 'absolute';
            tooltip.style.backgroundColor = '#333';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px 10px';
            tooltip.style.borderRadius = '3px';
            tooltip.style.fontSize = '0.85rem';
            tooltip.style.zIndex = '1000';
            tooltip.style.whiteSpace = 'nowrap';
            tooltip.textContent = this.getAttribute('title');

            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.left = (rect.left + window.scrollX) + 'px';
            tooltip.style.top = (rect.bottom + window.scrollY + 5) + 'px';

            this._tooltip = tooltip;
        });

        el.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                delete this._tooltip;
            }
        });
    });
}

/**
 * Inicializar menú móvil para navegadores pequeños
 */
function initMobileMenu() {
    const header = document.querySelector('header');
    if (!header) return;

    // Verificar si ya existe un menú móvil (evitar duplicados)
    if (document.querySelector('.mobile-menu-toggle')) return;

    // Crear botón de menú móvil
    const menuToggle = document.createElement('button');
    menuToggle.className = 'mobile-menu-toggle';
    menuToggle.innerHTML = '�☰';
    menuToggle.setAttribute('aria-label', 'Menú de navegación');
    menuToggle.setAttribute('aria-expanded', 'false');

    // Insertar después del branding
    const branding = header.querySelector('.site-branding');
    if (branding) {
        branding.parentNode.insertBefore(menuToggle, branding.nextSibling);
    }

    // Toggle del menú
    menuToggle.addEventListener('click', function() {
        const nav = header.querySelector('.site-navigation');
        if (nav) {
            const isExpanded = nav.classList.toggle('mobile-active');
            this.setAttribute('aria-expanded', isExpanded);
        }
    });

    // Cerrar menú al hacer clic en un enlace
    const navLinks = header.querySelectorAll('.site-navigation a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const nav = header.querySelector('.site-navigation');
            if (nav && nav.classList.contains('mobile-active')) {
                nav.classList.remove('mobile-active');
                const toggle = header.querySelector('.mobile-menu-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', false);
            }
        });
    });
}

/**
 * Inicializar alertas auto-ocultables
 */
function initAutoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    alerts.forEach(alert => {
        const delay = parseInt(alert.getAttribute('data-auto-dismiss')) || 5000; // 5 segundos por defecto

        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }
        }, delay);
    });
}

/**
 * Inicializar validación básica de formularios
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');

                    // Crear mensaje de error si no existe
                    let errorMsg = field.parentNode.querySelector('.invalid-feedback');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'invalid-feedback';
                        errorMsg.textContent = 'Este campo es requerido';
                        field.parentNode.appendChild(errorMsg);
                    }
                } else {
                    field.classList.remove('is-invalid');
                    const errorMsg = field.parentNode.querySelector('.invalid-feedback');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Inicializar carga diferida de imágenes (simulado para entrenar el concepto)
 */
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-lazy]');
    images.forEach(img => {
        // En una implementación real, se usaría Intersection Observer
        // Aquí simulamos la carga inmediata para el entrenamiento
        const src = img.getAttribute('data-lazy');
        if (src) {
            img.src = src;
            img.removeAttribute('data-lazy');
            img.classList.add('lazy-loaded');
        }
    });
}

/**
 * Inicializar contador regresivo para sesiones (simulado)
 */
function initSessionCountdown() {
    const countdownElement = document.getElementById('session-countdown');
    if (!countdownElement) return;

    let secondsLeft = parseInt(countdownElement.getAttribute('data-seconds')) || 1800; // 30 minutos por defecto

    const updateCountdown = () => {
        const minutes = Math.floor(secondsLeft / 60);
        const seconds = secondsLeft % 60;
        countdownElement.textContent =
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');

        if (secondsLeft <= 0) {
            countdownElement.textContent = '¡Sesión expirada!';
            // En una aplicación real, redirigiríamos al logout
            // window.location.href = '/logout';
        } else {
            secondsLeft--;
        }
    };

    // Actualizar inmediatamente y luego cada segundo
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

/**
 * Función de utilidad: mostrar notificación toast
 */
function showToast(message, type = 'info', duration = 3000) {
    // Crear contenedor de toasts si no existe
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        toastContainer.style.position = 'fixed';
        toastContainer.style.top = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '1050';
        document.body.appendChild(toastContainer);
    }

    // Crear toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.backgroundColor = type === 'success' ? '#28a745' :
                           type === 'error' ? '#dc3545' :
                           type === 'warning' ? '#ffc107' : '#17a2b8';
    toast.style.color = 'white';
    toast.style.padding = '1rem 1.5rem';
    toast.style.borderRadius = '0.25rem';
    toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '0.75rem';
    toast.style.marginBottom = '1rem';
    toast.style.animation = 'slideIn 0.3s forwards, fadeOut 0.3s forwards ' + (duration - 300) + 'ms';

    const icon = document.createElement('span');
    icon.innerHTML = type === 'success' ? '��������✓' :
                     type === 'error' ? '��������✗' :
                     type === 'warning' ? '��������⚠' : '�������ℹ��️';
    icon.style.fontSize = '1.25rem';

    const text = document.createElement('span');
    text.textContent = message;

    toast.appendChild(icon);
    toast.appendChild(text);
    toastContainer.appendChild(toast);

    // Remover toast después de la animación
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, duration);
}

/**
 * Función de utilidad: hacer petición AJAX simple
 */
function ajax(url, options = {}) {
    const {
        method = 'GET',
        data = null,
        headers = {},
        onSuccess = null,
        onError = null
    } = options;

    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url);

        // Establecer headers
        Object.keys(headers).forEach(key => {
            xhr.setRequestHeader(key, headers[key]);
        });

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = xhr.responseText;
                    try {
                        const json = JSON.parse(response);
                        if (onSuccess) onSuccess(json);
                        resolve(json);
                    } catch (e) {
                        if (onSuccess) onSuccess(response);
                        resolve(response);
                    }
                } else {
                    const error = {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        response: xhr.responseText
                    };
                    if (onError) onError(error);
                    reject(error);
                }
            }
        };

        xhr.onerror = function() {
            const error = {
                status: 0,
                statusText: 'Network Error',
                response: null
            };
            if (onError) onError(error);
            reject(error);
        };

        // Enviar datos
        if (data instanceof FormData) {
            xhr.send(data);
        } else if (typeof data === 'object') {
            xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            xhr.send(JSON.stringify(data));
        } else {
            xhr.send(data);
        }
    });
}

// Exportar funciones para uso global (si es necesario)
window.BlackForge = {
    showToast,
    ajax,
    initTooltips,
    initMobileMenu,
    initAutoDismissAlerts,
    initFormValidation,
    initLazyLoading,
    initSessionCountdown
};