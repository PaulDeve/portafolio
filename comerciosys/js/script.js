/**
 * JavaScript del sistema ComercioSys
 * Sistema de Gestión de Ventas
 */

// Variables globales
let currentDateTime = null;

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Inicializar la aplicación
 */
function initializeApp() {
    // Actualizar fecha y hora cada segundo
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Inicializar formularios
    initializeForms();
    
    // Inicializar modales
    initializeModals();
    
    // Inicializar cálculos automáticos
    initializeCalculations();
}

/**
 * Actualizar fecha y hora actual
 */
function updateDateTime() {
    const datetimeElement = document.getElementById('current-datetime');
    if (datetimeElement) {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        datetimeElement.textContent = now.toLocaleDateString('es-ES', options);
    }
}

/**
 * Inicializar formularios
 */
function initializeForms() {
    // Formulario de ventas
    const ventaForm = document.getElementById('venta-form');
    if (ventaForm) {
        ventaForm.addEventListener('submit', validateVentaForm);
    }
    
    // Formulario de usuarios
    const usuarioForm = document.getElementById('usuario-form');
    if (usuarioForm) {
        usuarioForm.addEventListener('submit', validateUsuarioForm);
    }
    
    // Formulario de login
    const loginForm = document.querySelector('.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }
}

/**
 * Inicializar modales
 */
function initializeModals() {
    // Modal de eliminación
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        // Cerrar modal al hacer clic en el botón de cerrar
        const closeButtons = deleteModal.querySelectorAll('.modal-close');
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                hideModal('deleteModal');
            });
        });
        
        // Cerrar modal al hacer clic fuera del contenido
        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                hideModal('deleteModal');
            }
        });
        
        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && deleteModal.classList.contains('show')) {
                hideModal('deleteModal');
            }
        });
    }
}

/**
 * Inicializar cálculos automáticos
 */
function initializeCalculations() {
    // Cálculo automático del total en ventas
    const cantidadInput = document.getElementById('cantidad');
    const precioInput = document.getElementById('precio_unitario');
    const totalInput = document.getElementById('total');
    
    if (cantidadInput && precioInput && totalInput) {
        function calculateTotal() {
            const cantidad = parseFloat(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.value) || 0);
            const total = cantidad * precio;
            
            totalInput.value = formatCurrency(total);
        }
        
        cantidadInput.addEventListener('input', calculateTotal);
        precioInput.addEventListener('input', calculateTotal);
        
        // Calcular total inicial si hay valores
        calculateTotal();
    }
}

/**
 * Validar formulario de ventas
 */
function validateVentaForm(e) {
    const form = e.target;
    const cantidad = form.querySelector('#cantidad').value;
    const precio = form.querySelector('#precio_unitario').value;
    const concepto = form.querySelector('#concepto').value;
    const fecha = form.querySelector('#fecha').value;
    const hora = form.querySelector('#hora').value;
    
    let errors = [];
    
    if (!concepto.trim()) {
        errors.push('El concepto es requerido');
    }
    
    if (!fecha) {
        errors.push('La fecha es requerida');
    }
    
    if (!hora) {
        errors.push('La hora es requerida');
    }
    
    if (!cantidad || cantidad <= 0) {
        errors.push('La cantidad debe ser mayor a 0');
    }
    
    if (!precio || precio <= 0) {
        errors.push('El precio unitario debe ser mayor a 0');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        showAlert('error', 'Por favor corrija los siguientes errores:', errors);
    }
}

/**
 * Validar formulario de usuarios
 */
function validateUsuarioForm(e) {
    const form = e.target;
    const idCod = form.querySelector('#id_cod').value;
    const apellido = form.querySelector('#apellido').value;
    const nombre = form.querySelector('#nombre').value;
    const nick = form.querySelector('#nick').value;
    const pass = form.querySelector('#pass').value;
    const rol = form.querySelector('#rol').value;
    
    let errors = [];
    
    if (!idCod || idCod < 100) {
        errors.push('El ID debe ser mayor o igual a 100');
    }
    
    if (!apellido.trim()) {
        errors.push('El apellido es requerido');
    }
    
    if (!nombre.trim()) {
        errors.push('El nombre es requerido');
    }
    
    if (!nick.trim()) {
        errors.push('El nombre de usuario es requerido');
    }
    
    if (!pass.trim()) {
        errors.push('La contraseña es requerida');
    }
    
    if (!rol) {
        errors.push('El rol es requerido');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        showAlert('error', 'Por favor corrija los siguientes errores:', errors);
    }
}

/**
 * Validar formulario de login
 */
function validateLoginForm(e) {
    const form = e.target;
    const nick = form.querySelector('#nick').value;
    const password = form.querySelector('#password').value;
    
    if (!nick.trim() || !password.trim()) {
        e.preventDefault();
        showAlert('error', 'Por favor complete todos los campos');
    }
}

/**
 * Mostrar modal
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Ocultar modal
 */
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

/**
 * Eliminar venta
 */
function eliminarVenta(id) {
    const deleteForm = document.getElementById('deleteForm');
    const deleteId = document.getElementById('deleteId');
    
    if (deleteForm && deleteId) {
        deleteId.value = id;
        showModal('deleteModal');
    }
}

/**
 * Eliminar usuario
 */
function eliminarUsuario(id) {
    const deleteForm = document.getElementById('deleteForm');
    const deleteId = document.getElementById('deleteId');
    
    if (deleteForm && deleteId) {
        deleteId.value = id;
        showModal('deleteModal');
    }
}

/**
 * Formatear moneda
 */
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

/**
 * Mostrar alerta
 */
function showAlert(type, message, details = null) {
    // Crear elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    
    let content = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>`;
    content += `<span>${message}</span>`;
    
    if (details && Array.isArray(details)) {
        content += '<ul style="margin-top: 0.5rem; padding-left: 1rem;">';
        details.forEach(detail => {
            content += `<li>${detail}</li>`;
        });
        content += '</ul>';
    }
    
    alertDiv.innerHTML = content;
    
    // Insertar al inicio del contenido principal
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
}

/**
 * Confirmar acción
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Limpiar formulario
 */
function clearForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        
        // Limpiar campos calculados
        const totalInput = form.querySelector('#total');
        if (totalInput) {
            totalInput.value = '$0.00';
        }
    }
}

/**
 * Validar email (si se necesita en el futuro)
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validar número
 */
function validateNumber(value, min = null, max = null) {
    const num = parseFloat(value);
    if (isNaN(num)) return false;
    if (min !== null && num < min) return false;
    if (max !== null && num > max) return false;
    return true;
}

/**
 * Debounce para búsquedas
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Búsqueda en tiempo real (para futuras implementaciones)
 */
function initializeSearch() {
    const searchInputs = document.querySelectorAll('input[type="search"], input[data-search]');
    
    searchInputs.forEach(input => {
        const debouncedSearch = debounce((value) => {
            // Implementar búsqueda en tiempo real
            console.log('Buscando:', value);
        }, 300);
        
        input.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    });
}

/**
 * Exportar datos (para futuras implementaciones)
 */
function exportData(format = 'csv') {
    // Implementar exportación de datos
    console.log('Exportando datos en formato:', format);
}

/**
 * Imprimir página
 */
function printPage() {
    window.print();
}

/**
 * Generar reporte
 */
function generateReport(type, filters = {}) {
    // Implementar generación de reportes
    console.log('Generando reporte:', type, filters);
}

/**
 * Cargar datos con AJAX (para futuras implementaciones)
 */
function loadData(url, callback) {
    fetch(url)
        .then(response => response.json())
        .then(data => callback(data))
        .catch(error => {
            console.error('Error al cargar datos:', error);
            showAlert('error', 'Error al cargar los datos');
        });
}

/**
 * Enviar datos con AJAX (para futuras implementaciones)
 */
function sendData(url, data, callback) {
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => callback(data))
    .catch(error => {
        console.error('Error al enviar datos:', error);
        showAlert('error', 'Error al enviar los datos');
    });
}

/**
 * Mostrar loading
 */
function showLoading(element) {
    if (element) {
        element.style.opacity = '0.5';
        element.style.pointerEvents = 'none';
        
        const loading = document.createElement('div');
        loading.className = 'loading';
        loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
        loading.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        `;
        
        element.style.position = 'relative';
        element.appendChild(loading);
    }
}

/**
 * Ocultar loading
 */
function hideLoading(element) {
    if (element) {
        element.style.opacity = '1';
        element.style.pointerEvents = 'auto';
        
        const loading = element.querySelector('.loading');
        if (loading) {
            loading.remove();
        }
    }
}

/**
 * Inicializar tooltips (para futuras implementaciones)
 */
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', (e) => {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = e.target.dataset.tooltip;
            tooltip.style.cssText = `
                position: absolute;
                background: #333;
                color: white;
                padding: 0.5rem;
                border-radius: 4px;
                font-size: 0.875rem;
                z-index: 1000;
                pointer-events: none;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = e.target.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            
            e.target.tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', (e) => {
            if (e.target.tooltip) {
                e.target.tooltip.remove();
                e.target.tooltip = null;
            }
        });
    });
}

// Exportar funciones para uso global
window.ComercioSys = {
    showModal,
    hideModal,
    eliminarVenta,
    eliminarUsuario,
    formatCurrency,
    showAlert,
    confirmAction,
    clearForm,
    validateEmail,
    validateNumber,
    exportData,
    printPage,
    generateReport,
    loadData,
    sendData,
    showLoading,
    hideLoading
};
