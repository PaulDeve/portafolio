<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Solo administradores
if ($_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['accion'] == 'editar') {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, usuario = ?, rol = ? WHERE id_usuario = ?");
        $stmt->execute([$_POST['nombre'], $_POST['usuario'], $_POST['rol'], $_POST['id_usuario']]);
        echo "<script>Swal.fire('Usuario actualizado', '', 'success');</script>";
    } elseif ($_POST['accion'] == 'eliminar') {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$_POST['id_usuario']]);
        echo "<script>Swal.fire('Usuario eliminado', '', 'success');</script>";
    }
}

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content flex-grow-1">
            <?php include '../includes/navbar.php'; ?>
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h2><i class="fas fa-user-cog" style="color: var(--primary);"></i> Gestión de Usuarios</h2>
                    </div>
                    <div class="col text-end">
                        <div class="mb-3">
                            <a href="../auth/register.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nuevo Usuario
                            </a>
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
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= $usuario['id_usuario'] ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                    <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                                    <td>
                                        <?php
                                            $rol_color = 'var(--text-light)'; // vendedor
                                            if ($usuario['rol'] == 'admin') $rol_color = 'var(--primary)';
                                            if ($usuario['rol'] == 'optometrista') $rol_color = 'var(--accent)';
                                        ?>
                                        <span class="badge" style="background-color: <?= $rol_color ?>;">
                                            <?= ucfirst($usuario['rol']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editarUsuario(<?= htmlspecialchars(json_encode($usuario)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?= $usuario['id_usuario'] ?>)">
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

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_usuario" id="id_usuario">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="usuario" id="usuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select name="rol" id="rol" class="form-select" required>
                                <option value="vendedor">Vendedor</option>
                                <option value="optometrista">Optometrista</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editarUsuario(usuario) {
            document.getElementById('id_usuario').value = usuario.id_usuario;
            document.getElementById('nombre').value = usuario.nombre;
            document.getElementById('usuario').value = usuario.usuario;
            document.getElementById('rol').value = usuario.rol;
            new bootstrap.Modal(document.getElementById('modalUsuario')).show();
        }

        function eliminarUsuario(id) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id_usuario" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    <?php include '../includes/footer.php'; ?>