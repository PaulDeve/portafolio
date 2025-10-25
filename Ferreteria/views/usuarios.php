<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/../includes/header.php';
$db = $conexion_global->getPdo();
$users = $db->query('SELECT id_usuario,nombre_usuario,usuario,rol FROM usuarios ORDER BY id_usuario DESC')->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2>Usuarios</h2>
	<a href="/Ferreteria/views/usuario_form.php" class="btn btn-primary">Nuevo Usuario</a>
</div>

<table class="table table-striped">
	<thead><tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Acciones</th></tr></thead>
	<tbody>
		<?php foreach($users as $u): ?>
		<tr>
			<td><?= $u['id_usuario'] ?></td>
			<td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
			<td><?= htmlspecialchars($u['usuario']) ?></td>
			<td><?= htmlspecialchars($u['rol']) ?></td>
			<td>
				<a href="/Ferreteria/views/usuario_form.php?id=<?= $u['id_usuario'] ?>" class="btn btn-sm btn-warning">Editar</a>
				<?php if(isset($_SESSION['rol']) && $_SESSION['rol']==='admin'): ?>
					<a href="/Ferreteria/controllers/usuariosController.php?action=delete&id=<?= $u['id_usuario'] ?>" data-confirm="¿Eliminar usuario?" class="btn btn-sm btn-danger">Eliminar</a>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

