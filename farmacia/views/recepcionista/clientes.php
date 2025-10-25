<?php
require_once '../../controllers/AuthController.php';
$auth = new AuthController();
$auth->checkAuth();
$auth->checkRole(['administrador', 'recepcionista']);

include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div id="page-content-wrapper">
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fs-4 mb-0">Gestión de Clientes</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clienteModal">
                        <i class="bi bi-person-plus"></i> Nuevo Cliente
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargan dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title">Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="clienteForm">
                    <input type="hidden" id="cliente_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">DNI</label>
                        <input type="text" class="form-control app-form-control" id="dni" name="dni" maxlength="8" 
                               pattern="[0-9]{8}" title="Ingrese 8 dígitos">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control app-form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control app-form-control" id="telefono" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control app-form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control app-form-control" id="direccion" name="direccion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select app-form-select" id="estado" name="estado">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCliente()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historial -->
<div class="modal fade" id="historialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title">Historial de Compras</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Comprobante</th>
                                <th>Items</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="historialBody">
                            <!-- Los datos se cargan dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>

<script>
let dataTable;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    dataTable = $('.datatable').DataTable({
        ajax: {
            url: '/farmacia/controllers/ClienteController.php',
            type: 'POST',
            data: function(d) {
                d.action = 'listar';
            },
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'dni' },
            { data: 'nombre' },
            { data: 'telefono' },
            { data: 'email' },
            {
                data: 'estado',
                render: function(data) {
                    return data == 1 ? 
                        '<span class="badge bg-success">Activo</span>' : 
                        '<span class="badge bg-danger">Inactivo</span>';
                }
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-info" onclick="editarCliente(${data.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarCliente(${data.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="verHistorial(${data.id})">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: '/farmacia/assets/vendor/datatables/i18n/es-ES.json'
        }
    });
});

function limpiarFormulario() {
    document.getElementById('clienteForm').reset();
    document.getElementById('cliente_id').value = '';
}

function editarCliente(id) {
    fetch('/farmacia/controllers/ClienteController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=obtener&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const cliente = data.data;
            document.getElementById('cliente_id').value = cliente.id; // FIX: Explicitly set the hidden ID field
            Object.keys(cliente).forEach(key => {
                const input = document.getElementById(key);
                if (input) input.value = cliente[key];
            });
            $('#clienteModal').modal('show');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
}

function guardarCliente() {
    if (!validateForm('clienteForm')) return;

    const formData = new FormData(document.getElementById('clienteForm'));
    formData.append('action', formData.get('id') ? 'actualizar' : 'crear');

    fetch('/farmacia/controllers/ClienteController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => { // FIX: Chain actions to the promise
                $('#clienteModal').modal('hide');
                dataTable.ajax.reload(null, false);
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
}

function eliminarCliente(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/farmacia/controllers/ClienteController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=eliminar&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    dataTable.ajax.reload();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
}

function verHistorial(id) {
    fetch('/farmacia/controllers/ClienteController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=historial&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const tbody = document.getElementById('historialBody');
            tbody.innerHTML = '';

            data.data.forEach(venta => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${formatDate(venta.fecha_venta)}</td>
                    <td>${venta.tipo_comprobante.toUpperCase()} - ${venta.codigo}</td>
                    <td>${venta.items}</td>
                    <td>${formatCurrency(venta.total)}</td>
                `;
                tbody.appendChild(tr);
            });

            $('#historialModal').modal('show');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Validar DNI al escribir
document.getElementById('dni').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Validar teléfono al escribir
document.getElementById('telefono').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// Limpiar formulario al abrir el modal
$('#clienteModal').on('show.bs.modal', function(e) {
    if (!e.relatedTarget) return; // No limpiar si se abre para editar
    limpiarFormulario();
});
</script>