<?php
require_once '../../controllers/AuthController.php';
$auth = new AuthController();
$auth->checkAuth();
$auth->checkRole(['vendedor', 'administrador']);

include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div id="page-content-wrapper">
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fs-4 mb-0">Nueva Venta</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Formulario de Venta -->
            <div class="col-md-8">
                <div class="card mb-4 bg-dark text-white border-secondary">
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Búsqueda de Productos -->
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" class="form-control app-form-control" id="buscarProducto" 
                                           placeholder="Buscar producto por nombre o código...">
                                    <button class="btn btn-primary app-btn-primary" type="button" onclick="buscarProducto()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <!-- Contenedor para resultados de búsqueda -->
                                <div id="searchResultsContainer" class="position-relative">
                                    <div class="list-group position-absolute w-100" style="z-index: 1000;">
                                        <!-- Los resultados se insertarán aquí -->
                                    </div>
                                </div>
                            </div>

                            <!-- Espacio para la tabla de resultados (alternativa) -->
                            <div class="col-md-12" id="search-results-table-container" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover" id="tablaResultadosBusqueda">
                                        <thead>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Stock</th>
                                            <th>Precio</th>
                                            <th>Acción</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabla de Productos Seleccionados -->
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover" id="tablaVenta">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Venta -->
            <div class="col-md-4">
                <div class="card bg-dark text-white border-secondary">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Resumen de Venta</h5>
                        <form id="ventaForm">
                            <div class="mb-3">
                                <label class="form-label">Cliente</label>
                                <select class="form-select app-form-select" id="cliente_id" name="cliente_id">
                                    <option value="">Cliente General</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo Comprobante</label>
                                <select class="form-select app-form-select" id="tipo_comprobante" name="tipo_comprobante" required>
                                    <option value="boleta">Boleta</option>
                                    <option value="factura">Factura</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control app-form-control" id="subtotal" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">IGV (18%)</label>
                                <input type="text" class="form-control app-form-control" id="igv" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total</label>
                                <input type="text" class="form-control app-form-control" id="total" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Efectivo</label>
                                <input type="number" class="form-control app-form-control" id="efectivo" step="0.10" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vuelto</label>
                                <input type="text" class="form-control app-form-control" id="vuelto" readonly>
                            </div>
                            <button type="button" class="btn btn-primary app-btn-primary w-100" onclick="registrarVenta()">
                                Registrar Venta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>

<script>
let productosVenta = [];
let timeout = null;

// Autocompletado de productos
document.getElementById('buscarProducto').addEventListener('keyup', function(e) {
    clearTimeout(timeout);
    const termino = this.value.trim();
    if (termino.length > 0) { // Buscar si hay algún texto
        timeout = setTimeout(buscarProducto, 300); // Debounce search
    } else { // Si el campo está vacío, ocultar resultados inmediatamente
        document.getElementById('search-results-table-container').style.display = 'none';
        document.querySelector('#tablaResultadosBusqueda tbody').innerHTML = '';
    }
});

function buscarProducto() {
    const termino = document.getElementById('buscarProducto').value.trim();
    const resultsTableContainer = document.getElementById('search-results-table-container');
    const tbody = document.querySelector('#tablaResultadosBusqueda tbody');

    if (termino.length === 0) {
        resultsTableContainer.style.display = 'none';
        tbody.innerHTML = ''; // Limpiar resultados anteriores
        return;
    }
    fetch('/farmacia/controllers/VentaController.php', {
    fetch('/farmacia/controllers/venta_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=buscar_producto&termino=${termino}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            mostrarResultadosBusqueda(data.data);
        } else {
            mostrarResultadosBusqueda([]); // Limpiar resultados si hay error o no hay datos
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarResultadosBusqueda([]); // Limpiar resultados en caso de error de fetch
    });
}

function mostrarResultadosBusqueda(productos) {
    const tbody = document.querySelector('#tablaResultadosBusqueda tbody');
    tbody.innerHTML = '';

    productos.forEach(producto => {
        const tr = document.createElement('tr');

        const codigoTd = document.createElement('td');
        codigoTd.textContent = producto.codigo;
        tr.appendChild(codigoTd);

        const nombreTd = document.createElement('td');
        nombreTd.textContent = producto.nombre;
        tr.appendChild(nombreTd);

        const stockTd = document.createElement('td');
        stockTd.textContent = producto.stock;
        tr.appendChild(stockTd);

        const precioTd = document.createElement('td');
        precioTd.textContent = formatCurrency(producto.precio_venta);
        tr.appendChild(precioTd);

        const actionTd = document.createElement('td');
        const button = document.createElement('button');
        button.className = 'btn btn-sm btn-primary';
        button.innerHTML = '<i class="bi bi-plus-lg"></i>';
        button.onclick = function() {
            agregarProducto(producto);
        };
        actionTd.appendChild(button);
        tr.appendChild(actionTd);

        tbody.appendChild(tr);
    });

    const resultsContainer = document.getElementById('search-results-table-container');
    if (productos.length > 0) {
        resultsContainer.style.display = 'block';
    } else {
        resultsContainer.style.display = 'none';
    }
}

function agregarProducto(producto) {
    // Verificar si ya existe
    const existente = productosVenta.find(p => p.id === producto.id);
    if (existente) {
        Swal.fire('Advertencia', 'El producto ya está en la lista', 'warning');
        return;
    }

    // Solicitar cantidad
    Swal.fire({
        title: 'Cantidad',
        inputLabel: `Ingrese la cantidad para ${producto.nombre} (Stock: ${producto.stock})`,
        inputPlaceholder: 'Cantidad',
        input: 'number',
        background: '#343a40', // Fondo oscuro de Bootstrap
        color: '#f8f9fa', // Color de texto claro de Bootstrap
        inputAttributes: {
            min: 1,
            max: producto.stock,
            step: 1
        },
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || value <= 0) {
                return 'Ingrese una cantidad válida';
            }
            if (value > producto.stock) {
                return 'Stock insuficiente';
            }
        },
        customClass: {
            popup: 'swal2-dark-popup', // Clase personalizada para el popup principal
            input: 'swal2-dark-input', // Clase personalizada para el campo de entrada
            confirmButton: 'btn btn-primary', // Usar estilo de botón primario de Bootstrap
            cancelButton: 'btn btn-secondary' // Usar estilo de botón secundario de Bootstrap
        },
    }).then((result) => {
        if (result.isConfirmed) {
            const cantidad = parseInt(result.value);
            producto.cantidad = cantidad;
            producto.subtotal = producto.precio_venta * cantidad;
            productosVenta.push(producto);
            actualizarTablaVenta();
            document.getElementById('search-results-table-container').style.display = 'none';
            document.querySelector('#tablaResultadosBusqueda tbody').innerHTML = '';
            document.getElementById('buscarProducto').value = '';
        }
    });
}

function actualizarTablaVenta() {
    const tbody = document.querySelector('#tablaVenta tbody');
    tbody.innerHTML = '';

    productosVenta.forEach((producto, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${producto.nombre}</td>
            <td>${formatCurrency(producto.precio_venta)}</td>
            <td>${producto.cantidad}</td>
            <td>${formatCurrency(producto.subtotal)}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    calcularTotales();
}

function eliminarProducto(index) {
    productosVenta.splice(index, 1);
    actualizarTablaVenta();
}

function calcularTotales() {
    const subtotal = productosVenta.reduce((sum, p) => sum + p.subtotal, 0);
    const igv = subtotal * 0.18;
    const total = subtotal + igv;

    document.getElementById('subtotal').value = formatCurrency(subtotal);
    document.getElementById('igv').value = formatCurrency(igv);
    document.getElementById('total').value = formatCurrency(total);

    // Recalcular vuelto
    calcularVuelto();
}

document.getElementById('efectivo').addEventListener('input', calcularVuelto);

function calcularVuelto() {
    const efectivo = parseFloat(document.getElementById('efectivo').value) || 0;
    const total = productosVenta.reduce((sum, p) => sum + p.subtotal, 0) * 1.18;
    const vuelto = efectivo - total;

    document.getElementById('vuelto').value = formatCurrency(Math.max(0, vuelto));
}

function registrarVenta() {
    if (productosVenta.length === 0) {
        Swal.fire('Error', 'Agregue productos a la venta', 'error');
        return;
    }

    const efectivo = parseFloat(document.getElementById('efectivo').value) || 0;
    const total = productosVenta.reduce((sum, p) => sum + p.subtotal, 0) * 1.18;

    if (efectivo < total) {
        Swal.fire('Error', 'El efectivo es insuficiente', 'error');
        return;
    }

    const venta = {
        action: 'registrar',
        cliente_id: document.getElementById('cliente_id').value,
        tipo_comprobante: document.getElementById('tipo_comprobante').value,
        usuario_id: <?php echo $_SESSION['usuario_id']; ?>,
        productos: productosVenta
    };

    fetch('/farmacia/controllers/VentaController.php', {
    fetch('/farmacia/controllers/venta_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(venta)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Venta Registrada!',
                text: data.message,
                showCancelButton: true,
                confirmButtonText: 'Imprimir Comprobante',
                cancelButtonText: 'Cerrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(`/farmacia/views/vendedor/comprobante.php?id=${data.venta_id}`, '_blank');
                }
                // Limpiar venta
                productosVenta = [];
                actualizarTablaVenta();
                document.getElementById('ventaForm').reset();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Cargar clientes al inicio
fetch('/farmacia/controllers/ClienteController.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=listar'
})
.then(response => response.json())
.then(data => {
    if (data.status === 'success') {
        const select = document.getElementById('cliente_id');
        data.data.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id;
            option.textContent = cliente.nombre;
            select.appendChild(option);
        });
    }
})
.catch(error => console.error('Error:', error));

// Ocultar resultados si se hace clic fuera
document.addEventListener('click', function(event) {
    const searchContainer = document.getElementById('searchResultsContainer');
    const searchInput = document.getElementById('buscarProducto');
    if (!searchContainer.contains(event.target) && event.target !== searchInput) {
        document.getElementById('search-results-table-container').style.display = 'none';
    }
});
</script>