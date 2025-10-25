<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// --- INICIO DE LA LÓGICA DEL CARRITO ---

// 1. Asegurarse de que el carrito siempre sea un array.
// Esta es la corrección clave para el error "Cannot access offset of type string".
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// 2. Procesar acciones (agregar producto, eliminar, etc.)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Agregar producto al carrito
    if ($accion == 'agregar_producto' && isset($_POST['id_producto'])) {
        $id_producto = $_POST['id_producto'];
        $cantidad = (int)($_POST['cantidad'] ?? 1);

        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
        } else {
            $stmt = $pdo->prepare("SELECT nombre, precio FROM productos WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($producto) {
                $_SESSION['carrito'][$id_producto] = [
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $cantidad
                ];
            }
        }
    }

    // Eliminar producto del carrito
    if ($accion == 'eliminar_producto' && isset($_POST['id_producto'])) {
        unset($_SESSION['carrito'][$_POST['id_producto']]);
    }

    // Vaciar carrito
    if ($accion == 'vaciar_carrito') {
        $_SESSION['carrito'] = [];
    }

    // Redireccionar para evitar reenvío de formulario
    header("Location: ventas.php");
    exit();
}

// 3. Calcular el total del carrito
$total_carrito = 0;
foreach ($_SESSION['carrito'] as $producto) {
    $total_carrito += $producto['precio'] * $producto['cantidad'];
}

// --- FIN DE LA LÓGICA DEL CARRITO ---

// Obtener datos para los formularios
$productos = $pdo->query("SELECT id_producto, nombre, precio, stock FROM productos WHERE stock > 0 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$clientes = $pdo->query("SELECT id_cliente, nombre FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

?>

    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content flex-grow-1">
            <?php include '../includes/navbar.php'; ?>
            <div class="container-fluid">
                <h2><i class="fas fa-shopping-cart" style="color: var(--primary);"></i> Nueva Venta</h2>

                <div class="row">
                    <!-- Columna de Productos -->
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Seleccionar Productos</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="accion" value="agregar_producto">
                                    <select name="id_producto" class="form-select" required>
                                        <option value="">-- Elige un producto --</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto['id_producto'] ?>">
                                                <?= htmlspecialchars($producto['nombre']) ?> (S/ <?= number_format($producto['precio'], 2) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="number" name="cantidad" class="form-control" value="1" min="1" style="width: 80px;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Columna del Carrito -->
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Carrito de Compras</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($_SESSION['carrito'])): ?>
                                    <p class="text-center text-muted">El carrito está vacío.</p>
                                <?php else: ?>
                                    <table class="table table-sm">
                                        <tbody>
                                            <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                                                    <td>x <?= $item['cantidad'] ?></td>
                                                    <td>S/ <?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                                                    <td>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="accion" value="eliminar_producto">
                                                            <input type="hidden" name="id_producto" value="<?= $id ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm p-0 px-1">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Total:</h5>
                                        <h5 class="mb-0">S/ <?= number_format($total_carrito, 2) ?></h5>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <form method="POST">
                                            <input type="hidden" name="accion" value="vaciar_carrito">
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">Vaciar Carrito</button>
                                        </form>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFinalizarVenta">
                                            Finalizar Venta <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Finalizar Venta -->
    <div class="modal fade" id="modalFinalizarVenta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalizar Venta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="procesar_venta.php"> <!-- Se recomienda un script separado -->
                    <div class="modal-body">
                        <p>Total a pagar: <strong>S/ <?= number_format($total_carrito, 2) ?></strong></p>
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select name="id_cliente" class="form-select" required>
                                <option value="">Seleccione un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar y Guardar Venta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>