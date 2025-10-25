<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
require_once __DIR__ . '/../includes/header.php';

$db = $conexion_global->getPdo();
$page = intval($_GET['page'] ?? 1); if ($page<1) $page=1;
$perPage = 10;
$search = $_GET['q'] ?? '';
$params = [];
$where = '';
if ($search) { $where = "WHERE nombre LIKE ? OR dni LIKE ?"; $params = ["%$search%","%$search%"]; }
$total = $db->prepare("SELECT COUNT(*) FROM clientes $where");
$total->execute($params);
$totalRows = $total->fetchColumn();
$pages = ceil($totalRows / $perPage);
$offset = ($page-1)*$perPage;
$stmt = $db->prepare("SELECT * FROM clientes $where ORDER BY id_cliente DESC LIMIT $offset,$perPage");
$stmt->execute($params);
$clientes = $stmt->fetchAll();

?>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2>Clientes</h2>
	<a href="/Ferreteria/views/cliente_form.php" class="btn btn-primary">Nuevo Cliente</a>
</div>

<div class="mb-3 d-flex">
	<input id="searchInput" class="form-control me-2" placeholder="Buscar por nombre o DNI" value="<?= htmlspecialchars($search) ?>">
	<button id="searchBtn" class="btn btn-outline-secondary">Buscar</button>
</div>

<div id="clientesList">
	<table class="table table-striped">
		<thead><tr><th>ID</th><th>Nombre</th><th>DNI</th><th>Teléfono</th><th>Dirección</th><th>Acciones</th></tr></thead>
		<tbody>
			<?php foreach($clientes as $c): ?>
			<tr>
				<td><?= $c['id_cliente'] ?></td>
				<td><?= htmlspecialchars($c['nombre']) ?></td>
				<td><?= htmlspecialchars($c['dni']) ?></td>
				<td><?= htmlspecialchars($c['telefono']) ?></td>
				<td><?= htmlspecialchars($c['direccion']) ?></td>
				<td>
					<a href="/Ferreteria/views/cliente_form.php?id=<?= $c['id_cliente'] ?>" class="btn btn-sm btn-warning">Editar</a>
					<a href="/Ferreteria/controllers/clientesController.php?action=delete&id=<?= $c['id_cliente'] ?>" data-confirm="¿Eliminar cliente?" class="btn btn-sm btn-danger">Eliminar</a>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<nav>
		<ul class="pagination">
			<?php for($i=1;$i<=$pages;$i++): ?>
				<li class="page-item <?= $i==$page? 'active':'' ?>"><a class="page-link" href="?page=<?=$i?>&q=<?=urlencode($search)?>"><?=$i?></a></li>
			<?php endfor; ?>
		</ul>
	</nav>
</div>

<script>
document.getElementById('searchBtn').addEventListener('click', function(){
	const q = document.getElementById('searchInput').value;
	window.location = '?q='+encodeURIComponent(q);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

?>
<h2>Clientes</h2>
<p>CRUD de clientes — Implementación pendiente (ejercicio similar a productos).</p>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
