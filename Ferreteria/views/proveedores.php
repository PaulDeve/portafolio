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
if ($search) { $where = "WHERE nombre LIKE ?"; $params = ["%$search%"]; }
$total = $db->prepare("SELECT COUNT(*) FROM proveedores $where");
$total->execute($params);
$totalRows = $total->fetchColumn();
$pages = ceil($totalRows / $perPage);
$offset = ($page-1)*$perPage;
$stmt = $db->prepare("SELECT * FROM proveedores $where ORDER BY id_proveedor DESC LIMIT $offset,$perPage");
$stmt->execute($params);
$provs = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2>Proveedores</h2>
	<a href="/Ferreteria/views/proveedor_form.php" class="btn btn-primary">Nuevo Proveedor</a>
</div>

<div class="mb-3 d-flex">
	<input id="searchInput" class="form-control me-2" placeholder="Buscar por nombre" value="<?= htmlspecialchars($search) ?>">
	<button id="searchBtn" class="btn btn-outline-secondary">Buscar</button>
</div>

<div id="provList">
	<table class="table table-striped">
		<thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Dirección</th><th>Acciones</th></tr></thead>
		<tbody>
			<?php foreach($provs as $p): ?>
			<tr>
				<td><?= $p['id_proveedor'] ?></td>
				<td><?= htmlspecialchars($p['nombre']) ?></td>
				<td><?= htmlspecialchars($p['telefono']) ?></td>
				<td><?= htmlspecialchars($p['direccion']) ?></td>
				<td>
					<a href="/Ferreteria/views/proveedor_form.php?id=<?= $p['id_proveedor'] ?>" class="btn btn-sm btn-warning">Editar</a>
					<a href="/Ferreteria/controllers/proveedoresController.php?action=delete&id=<?= $p['id_proveedor'] ?>" data-confirm="¿Eliminar proveedor?" class="btn btn-sm btn-danger">Eliminar</a>
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
