<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['accion'] == 'agendar') {
        $stmt = $pdo->prepare("INSERT INTO citas (id_cliente, id_usuario, fecha, hora, estado) VALUES (?, ?, ?, ?, 'pendiente')");
        $stmt->execute([$_POST['id_cliente'], $_SESSION['id_usuario'], $_POST['fecha'], $_POST['hora']]);
        echo "<script>Swal.fire('Cita agendada', '', 'success');</script>";
    } elseif ($_POST['accion'] == 'actualizar_estado') {
        $stmt = $pdo->prepare("UPDATE citas SET estado = ? WHERE id_cita = ?");
        $stmt->execute([$_POST['estado'], $_POST['id_cita']]);
        echo "<script>Swal.fire('Estado actualizado', '', 'success');</script>";
    }
}

// Obtener citas
$citas = $pdo->query("
    SELECT c.id_cita, c.fecha, c.hora, c.estado, 
           cli.nombre AS cliente, usu.nombre AS usuario
    FROM citas c
    JOIN clientes cli ON c.id_cliente = cli.id_cliente
    JOIN usuarios usu ON c.id_usuario = usu.id_usuario
    ORDER BY c.fecha DESC, c.hora DESC
")->fetchAll(PDO::FETCH_ASSOC);

$clientes = $pdo->query("SELECT id_cliente, nombre FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>
        <div class="main-content flex-grow-1">
            <?php include '../includes/navbar.php'; ?>
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h2><i class="fas fa-calendar-check" style="color: var(--primary);"></i> Gestión de Citas</h2>
                    </div>
                    <div class="col text-end">
                        <div class="mb-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCita">
                                <i class="fas fa-plus"></i> Nueva Cita
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table class="table table-modern table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Atendido por</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citas as $cita): ?>
                                <tr>
                                    <td><?= $cita['fecha'] ?></td>
                                    <td><?= $cita['hora'] ?></td>
                                    <td><?= htmlspecialchars($cita['cliente']) ?></td>
                                    <td><?= htmlspecialchars($cita['usuario']) ?></td>
                                    <td>
                                        <?php
                                            $badge_color = 'var(--text-light)'; // pendiente
                                            if ($cita['estado'] == 'atendida') $badge_color = 'var(--accent)';
                                            if ($cita['estado'] == 'cancelada') $badge_color = '#dc3545';
                                        ?>
                                        <span class="badge" style="background-color: <?= $badge_color ?>;">
                                            <?= ucfirst($cita['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="accion" value="actualizar_estado">
                                            <input type="hidden" name="id_cita" value="<?= $cita['id_cita'] ?>">
                                            <select name="estado" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                <option value="pendiente" <?= $cita['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                                <option value="atendida" <?= $cita['estado'] == 'atendida' ? 'selected' : '' ?>>Atendida</option>
                                                <option value="cancelada" <?= $cita['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                            </select>
                                        </form>
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

    <!-- Modal Nueva Cita -->
    <div class="modal fade" id="modalCita" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Cita</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="agendar">
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select name="id_cliente" class="form-select" required>
                                <option value="">Seleccione un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hora</label>
                            <input type="time" name="hora" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agendar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>