<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$db = $conexion_global->getPdo();
$page = intval($_GET['page'] ?? 1); if ($page<1) $page=1;
$perPage = 10;
$search = $_GET['q'] ?? '';
$params = [];
$where = '';
if ($search) { $where = "WHERE p.nombre LIKE ? OR p.categoria LIKE ?"; $params = ["%$search%","%$search%"]; }
$total = $db->prepare("SELECT COUNT(*) FROM productos p LEFT JOIN proveedores prov ON p.proveedor_id = prov.id_proveedor $where");
$total->execute($params);
$totalRows = $total->fetchColumn();
$pages = ceil($totalRows / $perPage);
$offset = ($page-1)*$perPage;
$stmt = $db->prepare("SELECT p.*, prov.nombre as proveedor FROM productos p LEFT JOIN proveedores prov ON p.proveedor_id = prov.id_proveedor $where ORDER BY p.id_producto DESC LIMIT $offset,$perPage");
$stmt->execute($params);
$productos = $stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/csrf.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Productos</h2>
  <div>
    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalProducto">Nuevo (rápido)</button>
    <a href="/Ferreteria/views/producto_form.php" class="btn btn-outline-primary">Nuevo (completo)</a>
  </div>
</div>

<!-- Modal crear producto rápido -->
<div class="modal fade" id="modalProducto" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="modalProductoForm" action="/Ferreteria/controllers/productosController.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="modal-header"><h5 class="modal-title">Nuevo Producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3"><label>Nombre</label><input name="nombre" class="form-control" required></div>
          <div class="mb-3"><label>Categoria</label><input name="categoria" class="form-control"></div>
          <div class="mb-3 row"><div class="col"><label>Precio compra</label><input name="precio_compra" type="number" step="0.01" class="form-control" value="0.00"></div><div class="col"><label>Precio venta</label><input name="precio_venta" type="number" step="0.01" class="form-control" value="0.00"></div></div>
          <div class="mb-3"><label>Stock</label><input name="stock" type="number" class="form-control" value="0"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-success">Guardar</button></div>
      </form>
    </div>
  </div>
</div>

<div class="mb-3 d-flex">
  <input id="searchInput" class="form-control me-2" placeholder="Buscar por nombre o categoría" value="<?= htmlspecialchars($search) ?>">
  <button id="searchBtn" class="btn btn-outline-secondary">Buscar</button>
</div>

<div class="table-responsive">
  <table class="table table-striped" id="productosTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Categoria</th>
        <th>Precio Venta</th>
        <th>Stock</th>
        <th>Proveedor</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($productos as $p): ?>
        <tr>
          <td><?= $p['id_producto'] ?></td>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= htmlspecialchars($p['categoria']) ?></td>
          <td>$<?= number_format($p['precio_venta'],2) ?></td>
          <td><?= intval($p['stock']) ?></td>
          <td><?= htmlspecialchars($p['proveedor']) ?></td>
          <td>
            <a href="/Ferreteria/views/producto_form.php?id=<?= $p['id_producto'] ?>" class="btn btn-sm btn-warning">Editar</a>
            <a href="/Ferreteria/controllers/productosController.php?action=delete&id=<?= $p['id_producto'] ?>" data-confirm="¿Eliminar producto?" class="btn btn-sm btn-danger">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<nav>
  <ul class="pagination">
    <?php for($i=1;$i<=$pages;$i++): ?>
      <li class="page-item <?= $i==$page? 'active':'' ?>"><a class="page-link" href="?page=<?=$i?>&q=<?=urlencode($search)?>"><?=$i?></a></li>
    <?php endfor; ?>
  </ul>
</nav>

<script>
document.getElementById('searchBtn').addEventListener('click', function(){
  const q = document.getElementById('searchInput').value;
  window.location = '?q='+encodeURIComponent(q);
});
</script>

<script>
// AJAX submit modal
const modalForm = document.getElementById('modalProductoForm');
if (modalForm) {
  modalForm.addEventListener('submit', function(e){
    e.preventDefault();
    const data = new FormData(modalForm);
    fetch(modalForm.action, { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r=>r.json())
      .then(js=>{
        if (js.error) { Swal.fire('Error', js.error, 'error'); }
        else { Swal.fire('Éxito', js.success, 'success').then(()=> location.reload()); }
      }).catch(err=>{ Swal.fire('Error','No se pudo guardar','error'); });
  });
}
</script>

<script>
// Live search (debounced)
let timeout = null;
const input = document.getElementById('searchInput');
if (input) {
  input.addEventListener('input', function(){
    clearTimeout(timeout);
    timeout = setTimeout(()=>{
      const q = input.value;
      fetch('/Ferreteria/controllers/productosSearch.php?q='+encodeURIComponent(q))
        .then(r=>r.json())
        .then(data=>{
          const tbody = document.querySelector('#productosTable tbody');
          tbody.innerHTML = data.map(p=>`<tr><td>${p.id_producto}</td><td>${escapeHtml(p.nombre)}</td><td>${escapeHtml(p.categoria)}</td><td>$${parseFloat(p.precio_venta).toFixed(2)}</td><td>${p.stock}</td><td><a class="btn btn-sm btn-warning" href="/Ferreteria/views/producto_form.php?id=${p.id_producto}">Editar</a> <a class="btn btn-sm btn-danger" data-confirm="¿Eliminar producto?" href="/Ferreteria/controllers/productosController.php?action=delete&id=${p.id_producto}">Eliminar</a></td></tr>`).join('');
        });
    }, 350);
  });
}

function escapeHtml(text){ return text ? text.replace(/[&<>"]+/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }) : ''; }
</script>
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
