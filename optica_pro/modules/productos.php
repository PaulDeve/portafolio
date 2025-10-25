<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['accion'] == 'agregar') {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, tipo, precio, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['nombre'], $_POST['tipo'], $_POST['precio'], $_POST['stock']]);
        echo "<script>Swal.fire('Producto agregado', '', 'success');</script>";
    } elseif ($_POST['accion'] == 'editar') {
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, tipo = ?, precio = ?, stock = ? WHERE id_producto = ?");
        $stmt->execute([$_POST['nombre'], $_POST['tipo'], $_POST['precio'], $_POST['stock'], $_POST['id_producto']]);
        echo "<script>Swal.fire('Producto actualizado', '', 'success');</script>";
    } elseif ($_POST['accion'] == 'eliminar') {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->execute([$_POST['id_producto']]);
        echo "<script>Swal.fire('Producto eliminado', '', 'success');</script>";
    }
}

$productos = $pdo->query("SELECT * FROM productos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content flex-grow-1">
            <?php include '../includes/navbar.php'; ?>
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h2><i class="fas fa-glasses" style="color: var(--primary);"></i> Gestión de Productos</h2>
                    </div>
                    <div class="col text-end">
                        <div class="mb-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                                <i class="fas fa-plus"></i> Nuevo Producto
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-modern table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= $producto['id_producto'] ?></td>
                                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                    <td><?= ucfirst($producto['tipo']) ?></td>
                                    <td><?= number_format($producto['precio'], 2) ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?= $producto['stock'] > 10 ? 'var(--accent)' : '#ffc107' ?>;">
                                            <?= $producto['stock'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editarProducto(<?= htmlspecialchars(json_encode($producto)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarProducto(<?= $producto['id_producto'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProductoTitle">Nuevo Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formProducto">
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="agregar">
                        <input type="hidden" name="id_producto" id="id_producto">

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="montura">Montura</option>
                                <option value="lente">Lente</option>
                                <option value="cristal">Cristal</option>
                                <option value="accesorio">Accesorio</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" name="precio" id="precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" id="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editarProducto(producto) {
            document.getElementById('modalProductoTitle').textContent = 'Editar Producto';
            document.getElementById('accion').value = 'editar';
            document.getElementById('id_producto').value = producto.id_producto;
            document.getElementById('nombre').value = producto.nombre;
            document.getElementById('tipo').value = producto.tipo;
            document.getElementById('precio').value = producto.precio;
            document.getElementById('stock').value = producto.stock;
            new bootstrap.Modal(document.getElementById('modalProducto')).show();
        }

        function eliminarProducto(id) {
            Swal.fire({
                title: '¿Eliminar producto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id_producto" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    <?php include '../includes/footer.php'; ?>