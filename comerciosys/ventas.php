<?php
/**
 * Módulo de gestión de ventas
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
            $comprador_apellido = trim($_POST['comprador_apellido']);
            $comprador_nombre = trim($_POST['comprador_nombre']);
            $comprador_telefono = trim($_POST['comprador_telefono']);
            $comprador_email = trim($_POST['comprador_email']);
            $concepto = trim($_POST['concepto']);
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $cantidad = (int)$_POST['cantidad'];
            $precio_unitario = (float)$_POST['precio_unitario'];
            $precio_incluye_igv = isset($_POST['precio_incluye_igv']) ? true : false;
            // Si el precio ingresado ya incluye IGV, convertirlo a precio sin IGV
            if ($precio_incluye_igv) {
                $precio_unitario = calcularSubtotal($precio_unitario);
            }
            
            if (empty($comprador_apellido) || empty($comprador_nombre) || empty($comprador_telefono) || 
                empty($concepto) || empty($fecha) || empty($hora) || $cantidad <= 0 || $precio_unitario <= 0) {
                $error = 'Por favor, complete todos los campos correctamente.';
            } else {
                // Crear o encontrar comprador
                $comprador_id = crearOEncontrarComprador($comprador_apellido, $comprador_nombre, $comprador_telefono, $comprador_email);
                
                if ($comprador_id) {
                    if (crearVenta($comprador_id, $concepto, $fecha, $hora, $cantidad, $precio_unitario)) {
                        // Obtener el ID de la venta recién creada
                        $ventas = getAllVentas();
                        $ultima_venta = $ventas[0]; // La más reciente
                        
                        // Redirigir a la boleta
                        header('Location: boleta.php?id=' . $ultima_venta['id'] . '&print=1');
                        exit();
                    } else {
                        $error = 'Error al registrar la venta.';
                    }
                } else {
                    $error = 'Error al procesar los datos del comprador.';
                }
            }
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $concepto = trim($_POST['concepto']);
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $cantidad = (int)$_POST['cantidad'];
            $precio_unitario = (float)$_POST['precio_unitario'];
            $precio_incluye_igv = isset($_POST['precio_incluye_igv']) ? true : false;
            // Si el precio ingresado ya incluye IGV, convertirlo a precio sin IGV
            if ($precio_incluye_igv) {
                $precio_unitario = calcularSubtotal($precio_unitario);
            }
            
            if (empty($concepto) || empty($fecha) || empty($hora) || $cantidad <= 0 || $precio_unitario <= 0) {
                $error = 'Por favor, complete todos los campos correctamente.';
            } else {
                if (actualizarVenta($id, $concepto, $fecha, $hora, $cantidad, $precio_unitario)) {
                    $message = 'Venta actualizada exitosamente.';
                } else {
                    $error = 'Error al actualizar la venta.';
                }
            }
            break;
            
        case 'eliminar':
            $id = (int)$_POST['id'];
            if (eliminarVenta($id)) {
                $message = 'Venta eliminada exitosamente.';
            } else {
                $error = 'Error al eliminar la venta.';
            }
            break;
    }
}

// Obtener filtros
$filtro_fecha = $_GET['fecha'] ?? '';
$filtro_concepto = $_GET['concepto'] ?? '';

// Obtener ventas
$ventas = getAllVentas($filtro_fecha, $filtro_concepto);

// Obtener productos para el dropdown
$productos = getProductosUnicos();

// Obtener venta para editar
$venta_editar = null;
if ($action === 'editar' && isset($_GET['id'])) {
    $venta_editar = getVentaById((int)$_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧾 ComercioSys - Gestión de Ventas</title>
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
                <h2>Gestión de Ventas</h2>
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

            <!-- Formulario de venta -->
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-plus-circle"></i>
                        <?php echo $venta_editar ? 'Editar Venta' : 'Nueva Venta'; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" id="venta-form">
                        <input type="hidden" name="action" value="<?php echo $venta_editar ? 'editar' : 'crear'; ?>">
                        <?php if ($venta_editar): ?>
                            <input type="hidden" name="id" value="<?php echo $venta_editar['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="comprador_apellido">Apellido del Comprador *</label>
                                <input type="text" id="comprador_apellido" name="comprador_apellido" required
                                       value="<?php echo htmlspecialchars($venta_editar['apellido'] ?? ''); ?>"
                                       placeholder="Apellido del comprador">
                            </div>
                            
                            <div class="form-group">
                                <label for="comprador_nombre">Nombre del Comprador *</label>
                                <input type="text" id="comprador_nombre" name="comprador_nombre" required
                                       value="<?php echo htmlspecialchars($venta_editar['nombre'] ?? ''); ?>"
                                       placeholder="Nombre del comprador">
                            </div>
                            
                            <div class="form-group">
                                <label for="comprador_telefono">Teléfono *</label>
                                <input type="tel" id="comprador_telefono" name="comprador_telefono" required
                                       value="<?php echo htmlspecialchars($venta_editar['telefono'] ?? ''); ?>"
                                       placeholder="Número de teléfono">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="comprador_email">Email</label>
                                <input type="email" id="comprador_email" name="comprador_email"
                                       value="<?php echo htmlspecialchars($venta_editar['email'] ?? ''); ?>"
                                       placeholder="correo@ejemplo.com (opcional)">
                            </div>
                            
                            <div class="form-group">
                                <label for="concepto">Producto *</label>
                                <select id="concepto" name="concepto" required>
                                    <option value="">Seleccionar producto</option>
                                    <?php foreach ($productos as $producto): ?>
                                        <option value="<?php echo htmlspecialchars($producto['concepto']); ?>" 
                                                data-precio="<?php echo $producto['precio_unitario']; ?>"
                                                <?php echo ($venta_editar && $venta_editar['concepto'] == $producto['concepto']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($producto['concepto'] . ' - $' . number_format($producto['precio_unitario'], 2)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" id="fecha" name="fecha" required
                                       value="<?php echo $venta_editar['fecha'] ?? date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="hora">Hora</label>
                                <input type="time" id="hora" name="hora" required
                                       value="<?php echo $venta_editar['hora'] ?? date('H:i'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" id="cantidad" name="cantidad" min="1" required
                                       value="<?php echo $venta_editar['cantidad'] ?? ''; ?>"
                                       placeholder="Cantidad">
                            </div>
                            
                            <div class="form-group">
                                <label for="precio_unitario">Precio Unitario</label>
                                <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" min="0" required
                                       value="<?php echo $venta_editar['precio_unitario'] ?? '0.00'; ?>"
                                       placeholder="0.00">
                                <div class="mt-1">
                                    <label style="font-weight:normal; font-size:0.9rem;">
                                        <input type="checkbox" name="precio_incluye_igv" value="1"> Precio incluye IGV (18%)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="total">Total</label>
                                <input type="text" id="total" readonly
                                       value="<?php echo $venta_editar ? formatCurrency($venta_editar['total']) : '$0.00'; ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $venta_editar ? 'Actualizar Venta' : 'Registrar Venta'; ?>
                            </button>
                            <?php if ($venta_editar): ?>
                                <a href="ventas.php" class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-filter"></i>
                        Filtros de Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="filter-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha_filtro">Fecha</label>
                                <input type="date" id="fecha_filtro" name="fecha" 
                                       value="<?php echo htmlspecialchars($filtro_fecha); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="concepto_filtro">Concepto</label>
                                <input type="text" id="concepto_filtro" name="concepto" 
                                       value="<?php echo htmlspecialchars($filtro_concepto); ?>"
                                       placeholder="Buscar por concepto">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    Buscar
                                </button>
                                <a href="ventas.php" class="btn btn-outline">
                                    <i class="fas fa-times"></i>
                                    Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de ventas -->
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-list"></i>
                        Lista de Ventas
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($ventas)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No se encontraron ventas</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Comprador</th>
                                        <th>Concepto</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ventas as $venta): ?>
                                        <tr>
                                            <td><?php echo $venta['id']; ?></td>
                                            <td><?php echo htmlspecialchars($venta['nombre'] . ' ' . $venta['apellido']); ?></td>
                                            <td><?php echo htmlspecialchars($venta['concepto']); ?></td>
                                            <td><?php echo formatDate($venta['fecha']); ?></td>
                                            <td><?php echo formatTime($venta['hora']); ?></td>
                                            <td><?php echo $venta['cantidad']; ?></td>
                                            <td><?php echo formatCurrency($venta['precio_unitario']); ?></td>
                                            <td><strong><?php echo formatCurrency($venta['total']); ?></strong></td>
                                            <td>
                                                <a href="boleta.php?id=<?php echo $venta['id']; ?>" 
                                                   class="btn btn-sm btn-success" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="ventas.php?action=editar&id=<?php echo $venta['id']; ?>" 
                                                   class="btn btn-sm btn-outline">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="eliminarVenta(<?php echo $venta['id']; ?>)" 
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
                <p>¿Está seguro de que desea eliminar esta venta?</p>
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
        // Función para actualizar precio y total automáticamente
        function actualizarPrecioYTotal() {
            const conceptoSelect = document.getElementById('concepto');
            const precioInput = document.getElementById('precio_unitario');
            const cantidadInput = document.getElementById('cantidad');
            const totalInput = document.getElementById('total');
            
            // Actualizar precio unitario cuando se selecciona un producto
            conceptoSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const precio = selectedOption.getAttribute('data-precio');
                
                if (precio) {
                    precioInput.value = parseFloat(precio).toFixed(2);
                    calcularTotal();
                } else {
                    precioInput.value = '0.00';
                    totalInput.value = '$0.00';
                }
            });
            
            // Calcular total cuando cambia la cantidad
            cantidadInput.addEventListener('input', calcularTotal);
            
            function calcularTotal() {
                const cantidad = parseFloat(cantidadInput.value) || 0;
                const precio = parseFloat(precioInput.value) || 0;
                const total = cantidad * precio;
                
                totalInput.value = '$' + total.toFixed(2);
            }
        }
        
        // Ejecutar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarPrecioYTotal();
            
            // Si hay una venta para editar, actualizar el precio automáticamente
            const conceptoSelect = document.getElementById('concepto');
            if (conceptoSelect.value) {
                conceptoSelect.dispatchEvent(new Event('change'));
            }
        });
        
        // Función para eliminar venta
        function eliminarVenta(id) {
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
