<?php
/**
 * Módulo de registro de compradores/clientes
 * ComercioSys - Sistema de Gestión de Ventas
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Verificar que el usuario esté logueado
requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

// Procesar acciones
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'crear':
            $apellido = trim($_POST['apellido']);
            $nombre = trim($_POST['nombre']);
            $telefono = trim($_POST['telefono']);
            $email = trim($_POST['email']);
            $direccion = trim($_POST['direccion']);
            
            if (empty($apellido) || empty($nombre) || empty($telefono) || empty($email)) {
                $error = 'Por favor, complete todos los campos obligatorios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Por favor, ingrese un email válido.';
            } elseif (emailExists($email)) {
                $error = 'El email ya está registrado.';
            } else {
                if (crearComprador($apellido, $nombre, $telefono, $email, $direccion)) {
                    $message = 'Comprador registrado exitosamente.';
                } else {
                    $error = 'Error al registrar el comprador.';
                }
            }
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $apellido = trim($_POST['apellido']);
            $nombre = trim($_POST['nombre']);
            $telefono = trim($_POST['telefono']);
            $email = trim($_POST['email']);
            $direccion = trim($_POST['direccion']);
            
            if (empty($apellido) || empty($nombre) || empty($telefono) || empty($email)) {
                $error = 'Por favor, complete todos los campos obligatorios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Por favor, ingrese un email válido.';
            } elseif (emailExists($email, $id)) {
                $error = 'El email ya está registrado por otro comprador.';
            } else {
                if (actualizarComprador($id, $apellido, $nombre, $telefono, $email, $direccion)) {
                    $message = 'Comprador actualizado exitosamente.';
                } else {
                    $error = 'Error al actualizar el comprador.';
                }
            }
            break;
            
        case 'eliminar':
            $id = (int)$_POST['id'];
            
            if (eliminarComprador($id)) {
                $message = 'Comprador eliminado exitosamente.';
            } else {
                $error = 'Error al eliminar el comprador.';
            }
            break;
    }
}

// Obtener compradores
$compradores = getAllCompradores();

// Obtener comprador para editar
$comprador_editar = null;
if ($action === 'editar' && isset($_GET['id'])) {
    $comprador_editar = getCompradorById((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾 ComercioSys - Registro de Compradores</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>🧾 ComercioSys</h1>
            <div class="user-info">
                <span>Bienvenido, <?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></span>
                <span class="user-role"><?php echo htmlspecialchars($user['rol']); ?></span>
                <a href="logout.php" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h2>Registro de Compradores</h2>
                <div class="page-actions">
                    <a href="dashboard.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de comprador -->
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-user-plus"></i>
                        <?php echo $comprador_editar ? 'Editar Comprador' : 'Registrar Nuevo Comprador'; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" id="comprador-form">
                        <input type="hidden" name="action" value="<?php echo $comprador_editar ? 'editar' : 'crear'; ?>">
                        <?php if ($comprador_editar): ?>
                            <input type="hidden" name="id" value="<?php echo $comprador_editar['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="apellido">Apellido *</label>
                                <input type="text" id="apellido" name="apellido" required
                                       value="<?php echo htmlspecialchars($comprador_editar['apellido'] ?? ''); ?>"
                                       placeholder="Apellido del comprador">
                            </div>
                            
                            <div class="form-group">
                                <label for="nombre">Nombre *</label>
                                <input type="text" id="nombre" name="nombre" required
                                       value="<?php echo htmlspecialchars($comprador_editar['nombre'] ?? ''); ?>"
                                       placeholder="Nombre del comprador">
                            </div>
                            
                            <div class="form-group">
                                <label for="telefono">Teléfono *</label>
                                <input type="tel" id="telefono" name="telefono" required
                                       value="<?php echo htmlspecialchars($comprador_editar['telefono'] ?? ''); ?>"
                                       placeholder="Número de teléfono">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($comprador_editar['email'] ?? ''); ?>"
                                       placeholder="correo@ejemplo.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text" id="direccion" name="direccion"
                                       value="<?php echo htmlspecialchars($comprador_editar['direccion'] ?? ''); ?>"
                                       placeholder="Dirección del comprador (opcional)">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $comprador_editar ? 'Actualizar Comprador' : 'Registrar Comprador'; ?>
                            </button>
                            <?php if ($comprador_editar): ?>
                                <a href="usuarios.php" class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de compradores -->
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-users"></i>
                        Lista de Compradores Registrados
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($compradores)): ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>No se encontraron compradores registrados</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Apellido</th>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th>Dirección</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($compradores as $comprador): ?>
                                        <tr>
                                            <td><?php echo $comprador['id']; ?></td>
                                            <td><?php echo htmlspecialchars($comprador['apellido']); ?></td>
                                            <td><?php echo htmlspecialchars($comprador['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($comprador['telefono']); ?></td>
                                            <td><?php echo htmlspecialchars($comprador['email']); ?></td>
                                            <td><?php echo htmlspecialchars($comprador['direccion'] ?? 'No especificada'); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($comprador['fecha_registro'])); ?></td>
                                            <td>
                                                <a href="usuarios.php?action=editar&id=<?php echo $comprador['id']; ?>" 
                                                   class="btn btn-sm btn-outline">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="eliminarComprador(<?php echo $comprador['id']; ?>)" 
                                                        class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de confirmación para eliminar -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este comprador?</p>
                <p class="text-warning">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Eliminar
                    </button>
                    <button type="button" class="btn btn-outline modal-close">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Función para eliminar comprador
        function eliminarComprador(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteModal').classList.add('show');
        }

        // Cerrar modal
        document.querySelectorAll('.modal-close').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('deleteModal').classList.remove('show');
            });
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    </script>
    <script src="js/script.js"></script>
</body>
</html>
