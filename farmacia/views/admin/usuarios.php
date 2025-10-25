<?php
require_once '../../controllers/AuthController.php';
$auth = new AuthController();
$auth->checkAuth();
$auth->checkRole(['administrador']);

include '../layouts/header.php';
include '../layouts/sidebar.php';
?>

<div id="page-content-wrapper">
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fs-4 mb-0">Gestión de Usuarios</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#usuarioModal">
                        <i class="bi bi-person-plus"></i> Nuevo Usuario
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th>Email</th>
                                <th>Última Sesión</th>
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

<!-- Modal Usuario -->
<div class="modal fade" id="usuarioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title">Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="usuarioForm">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control app-form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" class="form-control app-form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control app-form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select app-form-select" id="rol" name="rol" required>
                            <option value="">Seleccionar...</option>
                            <option value="administrador">Administrador</option>
                            <option value="vendedor">Vendedor</option>
                            <option value="recepcionista">Recepcionista</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select app-form-select" id="estado" name="estado">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" class="form-control app-form-control" id="password" name="password">
                        <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Estadísticas -->
<div class="modal fade" id="statsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title">Estadísticas de Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">Total Ventas</h6>
                                <h2 class="card-title mb-0" id="totalVentas">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">Ventas Hoy</h6>
                                <h2 class="card-title mb-0" id="ventasHoy">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2">Total Ingresos</h6>
                                <h2 class="card-title mb-0" id="totalIngresos">S/0.00</h2>
                            </div>
                        </div>
                    </div>
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
            url: '/farmacia/controllers/UsuarioController.php',
            type: 'POST',
            data: function(d) {
                d.action = 'listar';
            },
            dataSrc: function(json) {
                return json.data || [];
            }
        },
        columns: [
            { data: 'usuario' },
            { data: 'nombre' },
            { 
                data: 'rol',
                render: function(data) {
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
            },
            { data: 'email' },
            {
                data: 'ultima_sesion',
                render: function(data) {
                    return data ? formatDate(data) : 'Nunca';
                }
            },
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
                    let buttons = `
                        <button class="btn btn-sm btn-info" onclick="editarUsuario(${data.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${data.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                    
                    if (data.rol === 'vendedor') {
                        buttons += `
                            <button class="btn btn-sm btn-secondary" onclick="verEstadisticas(${data.id})">
                                <i class="bi bi-graph-up"></i>
                            </button>
                        `;
                    }
                    
                    return buttons;
                }
            }
        ],
        language: {
            url: '/farmacia/assets/vendor/datatables/i18n/es-ES.json'
        }
    });
});

function limpiarFormulario() {
    document.getElementById('usuarioForm').reset();
    document.getElementById('id').value = '';
    document.getElementById('password').required = true;
}

function editarUsuario(id) {
    fetch('/farmacia/controllers/UsuarioController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=obtener&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const usuario = data.data;
            // Rellenar campos del formulario con los datos del usuario
            Object.keys(usuario).forEach(key => {
                const input = document.getElementById(key);
                if (input) input.value = usuario[key];
            });
            // Al editar, la contraseña no es obligatoria
            const pwd = document.getElementById('password');
            if (pwd) pwd.required = false;
            $('#usuarioModal').modal('show');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
}

function eliminarUsuario(id) {
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
            fetch('/farmacia/controllers/UsuarioController.php', {
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

function verEstadisticas(id) {
    fetch('/farmacia/controllers/UsuarioController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=estadisticas&id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const stats = data.data;
            document.getElementById('totalVentas').textContent = stats.total_ventas;
            document.getElementById('ventasHoy').textContent = stats.ventas_hoy;
            document.getElementById('totalIngresos').textContent = formatCurrency(stats.total_ingresos);
            $('#statsModal').modal('show');
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => console.error('Error:', error));
}

function guardarUsuario() {
    // Asignar 'required' a la contraseña solo si es un nuevo usuario
    const id = document.getElementById('id').value;
    const passwordInput = document.getElementById('password');
    if (!id) {
        passwordInput.required = true;
    } else {
        passwordInput.required = false;
    }

    if (!validateForm('usuarioForm')) return;

    const formData = new FormData(document.getElementById('usuarioForm'));
    formData.append('action', id ? 'actualizar' : 'crear');

    fetch('/farmacia/controllers/UsuarioController.php', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => {
        // Check if the response is valid JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.indexOf('application/json') !== -1) {
            return response.json();
        } else {
            return response.text().then(text => { 
                throw new Error("La respuesta no es un JSON válido:\n" + text);
            });
        }
    })
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });
            $('#usuarioModal').modal('hide');
            dataTable.ajax.reload();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error en guardarUsuario:', error);
        Swal.fire('Error', error.message, 'error');
    });
}

// Limpiar formulario al abrir el modal para nuevo usuario
$('#usuarioModal').on('show.bs.modal', function(e) {
    // Solo limpiar si el modal se abre con el botón de "Nuevo Usuario"
    if (e.relatedTarget && e.relatedTarget.hasAttribute('data-bs-target')) {
        limpiarFormulario();
    }
});

// Asegurar que el botón ejecuta la función correctamente
document.getElementById('btnGuardarUsuario').addEventListener('click', function(e) {
    e.preventDefault();
    try {
        guardarUsuario();
    } catch (err) {
        console.error('Error en guardarUsuario:', err);
        let alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger mt-3';
        alertDiv.textContent = 'Error al ejecutar guardarUsuario.';
        document.body.appendChild(alertDiv);
        setTimeout(() => { alertDiv.remove(); }, 3000);
    }
});
</script>