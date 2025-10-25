<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'agregar':
                $stmt = $pdo->prepare("INSERT INTO clientes (nombre, dni, correo, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['nombre'], $_POST['dni'], $_POST['correo'], $_POST['telefono'], $_POST['direccion']]);
                echo "<script>Swal.fire('Éxito', 'Cliente agregado correctamente', 'success');</script>";
                break;
                
            case 'editar':
                $stmt = $pdo->prepare("UPDATE clientes SET nombre = ?, dni = ?, correo = ?, telefono = ?, direccion = ? WHERE id_cliente = ?");
                $stmt->execute([$_POST['nombre'], $_POST['dni'], $_POST['correo'], $_POST['telefono'], $_POST['direccion'], $_POST['id_cliente']]);
                echo "<script>Swal.fire('Éxito', 'Cliente actualizado correctamente', 'success');</script>";
                break;
                
            case 'eliminar':
                $stmt = $pdo->prepare("DELETE FROM clientes WHERE id_cliente = ?");
                $stmt->execute([$_POST['id_cliente']]);
                echo "<script>Swal.fire('Éxito', 'Cliente eliminado correctamente', 'success');</script>";
                break;
        }
    }
}

// Obtener todos los clientes
$clientes = $pdo->query("SELECT * FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content flex-grow-1">
            <?php include '../includes/navbar.php'; ?>
            
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h2><i class="fas fa-users" style="color: var(--primary);"></i> Gestión de Clientes</h2>
                    </div>
                    <div class="col text-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCliente">
                            <i class="fas fa-plus"></i> Nuevo Cliente
                        </button>
                    </div>
                </div>
                
                <!-- Tabla de clientes -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-modern table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>DNI</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td><?php echo $cliente['id_cliente']; ?></td>
                                        <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['dni']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['correo']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editarCliente(<?php echo htmlspecialchars(json_encode($cliente)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarCliente(<?php echo $cliente['id_cliente']; ?>)">
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
    </div>
    
    <!-- Modal para agregar/editar cliente -->
    <div class="modal fade" id="modalCliente" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="formCliente">
                    <div class="modal-body">
                        <input type="hidden" name="accion" id="accion" value="agregar">
                        <input type="hidden" name="id_cliente" id="id_cliente">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" class="form-control" name="dni" id="dni" maxlength="8" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <textarea class="form-control" name="direccion" id="direccion" rows="2"></textarea>
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
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        function editarCliente(cliente) {
            document.getElementById('modalTitle').textContent = 'Editar Cliente';
            document.getElementById('accion').value = 'editar';
            document.getElementById('id_cliente').value = cliente.id_cliente;
            document.getElementById('nombre').value = cliente.nombre;
            document.getElementById('dni').value = cliente.dni;
            document.getElementById('correo').value = cliente.correo;
            document.getElementById('telefono').value = cliente.telefono;
            document.getElementById('direccion').value = cliente.direccion;
            
            new bootstrap.Modal(document.getElementById('modalCliente')).show();
        }
        
        function eliminarCliente(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="accion"