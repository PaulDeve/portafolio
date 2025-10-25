<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

if (strtolower($userRole) !== 'administrador') {
    echo "<script>window.location.href = '../auth/login.php';</script>";
    exit;
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Productos</h1>
        <button class="btn btn-primary shadow-sm" id="btnNuevoProducto"><i class="bi bi-plus-lg me-2"></i>Añadir Nuevo Producto</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Productos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="productsTable" width="100%" cellspacing="0"></table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm" novalidate>
                    <input type="hidden" id="producto_id" name="producto_id">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="codigo" class="form-label">Código</label><input type="text" class="form-control" id="codigo" name="codigo" required></div>
                        <div class="col-md-6 mb-3"><label for="nombre" class="form-label">Nombre</label><input type="text" class="form-control" id="nombre" name="nombre" required></div>
                    </div>
                    <div class="mb-3"><label for="descripcion" class="form-label">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea></div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="categoria" class="form-label">Categoría</label><input type="text" class="form-control" id="categoria" name="categoria"></div>
                        <div class="col-md-6 mb-3"><label for="proveedor" class="form-label">Proveedor</label><input type="text" class="form-control" id="proveedor" name="proveedor"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label for="stock" class="form-label">Stock</label><input type="number" class="form-control" id="stock" name="stock" value="0" required></div>
                        <div class="col-md-3 mb-3"><label for="stock_minimo" class="form-label">Stock Mínimo</label><input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="10" required></div>
                        <div class="col-md-3 mb-3"><label for="precio_compra" class="form-label">Precio Compra</label><input type="number" class="form-control" id="precio_compra" name="precio_compra" step="0.01" required></div>
                        <div class="col-md-3 mb-3"><label for="precio_venta" class="form-label">Precio Venta</label><input type="number" class="form-control" id="precio_venta" name="precio_venta" step="0.01" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="fecha_vencimiento" class="form-label">Fecha Vencimiento</label><input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento"></div>
                        <div class="col-md-6 mb-3"><label for="estado" class="form-label">Estado</label><select class="form-select" id="estado" name="estado"><option value="1">Activo</option><option value="0">Inactivo</option></select></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" form="productForm">Guardar Producto</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));
    const productForm = document.getElementById('productForm');
    const productModalLabel = document.getElementById('productModalLabel');
    const productsTable = new DataTable('#productsTable', {
        responsive: true, processing: true, serverSide: false,
        ajax: { url: '../../controllers/ProductoController.php?action=index', dataSrc: 'data' },
        columns: [
            { data: 'codigo', title: 'Código' }, { data: 'nombre', title: 'Nombre' },
            { data: 'categoria', title: 'Categoría' }, { data: 'stock', title: 'Stock' },
            { data: 'precio_venta', title: 'Precio Venta', render: data => `$${parseFloat(data).toFixed(2)}` },
            { data: 'estado', title: 'Estado', render: data => data == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>' },
            { data: 'id', title: 'Acciones', orderable: false, render: data => `
                <button class="btn btn-sm btn-info btn-edit" data-id="${data}"><i class="bi bi-pencil-square"></i></button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="${data}"><i class="bi bi-trash3-fill"></i></button>`
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/2.0.3/i18n/es-ES.json' },
        dom: '<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>'+'<'row'<'col-sm-12'tr>>'+'<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>',
    });

    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

    document.getElementById('btnNuevoProducto').addEventListener('click', () => {
        productForm.reset();
        document.getElementById('producto_id').value = '';
        productModalLabel.textContent = 'Nuevo Producto';
        productModal.show();
    });

    productForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const productoId = formData.get('producto_id');
        const url = productoId ? '../../controllers/ProductoController.php?action=update' : '../../controllers/ProductoController.php?action=store';

        fetch(url, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    productModal.hide();
                    Toast.fire({ icon: 'success', title: data.message });
                    productsTable.ajax.reload();
                } else {
                    Swal.fire('Error', data.message || 'Ocurrió un error', 'error');
                }
            })
            .catch(error => Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error'));
    });

    document.getElementById('productsTable').addEventListener('click', function(e) {
        const target = e.target.closest('.btn-edit, .btn-delete');
        if (!target) return;

        const id = target.dataset.id;

        if (target.classList.contains('btn-edit')) {
            fetch(`../../controllers/ProductoController.php?action=edit&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire('Error', data.error, 'error');
                        return;
                    }
                    Object.keys(data).forEach(key => {
                        const field = document.getElementById(key);
                        if (field) field.value = data[key];
                    });
                    document.getElementById('producto_id').value = data.id;
                    productModalLabel.textContent = 'Editar Producto';
                    productModal.show();
                });
        }

        if (target.classList.contains('btn-delete')) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, ¡bórralo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);
                    fetch('../../controllers/ProductoController.php?action=delete', { method: 'POST', body: formData })
                        .then(response => response.json())
                        ethen(data => {
                            if (data.status === 'success') {
                                Toast.fire({ icon: 'success', title: data.message });
                                productsTable.ajax.reload();
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar.', 'error');
                            }
                        });
                }
            });
        }
    });
});
</script>