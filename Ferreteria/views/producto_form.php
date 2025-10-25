<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$db = $conexion_global->getPdo();
$id = $_GET['id'] ?? null;
$producto = null;
if ($id) {
  $stmt = $db->prepare('SELECT * FROM productos WHERE id_producto = ?');
  $stmt->execute([$id]);
  $producto = $stmt->fetch();
}
$proveedores = $db->query('SELECT * FROM proveedores ORDER BY nombre')->fetchAll();
$categorias = $db->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL AND categoria <> '' ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/csrf.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2><?= $producto ? 'Editar' : 'Nuevo' ?> Producto</h2>
  <a href="/Ferreteria/views/productos.php" class="btn btn-secondary">Volver</a>
</div>

<form action="/Ferreteria/controllers/productosController.php" method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?? '' ?>">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input class="form-control" name="nombre" required value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea class="form-control" name="descripcion"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
  </div>
  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">Categoria</label>
      <div class="d-flex">
        <select name="categoria" id="selectCategoria" class="form-select">
          <option value="">-- Seleccionar --</option>
          <?php foreach($categorias as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= (isset($producto['categoria']) && $producto['categoria']==$cat)? 'selected':'' ?>><?= htmlspecialchars($cat) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="button" id="btnNewCatToggle" class="btn btn-outline-secondary ms-2" title="Nueva categoría">+</button>
      </div>
      <div id="newCatArea" class="mt-2" style="display:none;">
        <div class="input-group">
          <input type="text" id="newCategoria" class="form-control" placeholder="Nombre categoría">
          <button type="button" id="btnAddCategoria" class="btn btn-primary">Agregar</button>
        </div>
      </div>

      <div class="mt-2" id="categoriaBadges">
        <?php foreach($categorias as $cat): ?>
          <button type="button" class="btn btn-sm btn-outline-dark me-1 mb-1 category-badge" data-cat="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Precio compra</label>
      <input class="form-control" name="precio_compra" type="number" step="0.01" value="<?= $producto['precio_compra'] ?? '0.00' ?>">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">Precio venta</label>
      <input class="form-control" name="precio_venta" type="number" step="0.01" value="<?= $producto['precio_venta'] ?? '0.00' ?>">
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Stock</label>
      <input class="form-control" name="stock" type="number" value="<?= $producto['stock'] ?? '0' ?>">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Proveedor</label>
      <select name="proveedor_id" class="form-select">
        <option value="">-- Ninguno --</option>
        <?php foreach($proveedores as $prov): ?>
          <option value="<?= $prov['id_proveedor'] ?>" <?= (isset($producto['proveedor_id']) && $producto['proveedor_id']==$prov['id_proveedor'])? 'selected':'' ?>><?= htmlspecialchars($prov['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="d-grid">
    <button class="btn btn-success">Guardar</button>
  </div>
</form>

<script>
  // Delegated handlers for category UI (works even if elements are dynamic)
  document.addEventListener('click', function(e){
    // Toggle new category area
    const toggle = e.target.closest('#btnNewCatToggle');
    if (toggle) {
      const newArea = document.getElementById('newCatArea');
      if (newArea) newArea.style.display = newArea.style.display === 'none' ? 'block' : 'none';
      return;
    }

    // Add new category button
    const addBtn = e.target.closest('#btnAddCategoria');
    if (addBtn) {
      const newInput = document.getElementById('newCategoria');
      const select = document.getElementById('selectCategoria');
      if (!newInput || !select) return;
      const v = newInput.value.trim();
      if (!v) return;
      // case-insensitive check
      for (let i=0;i<select.options.length;i++){
        if (select.options[i].value.toLowerCase() === v.toLowerCase()) { select.value = select.options[i].value; newInput.value=''; return; }
      }
      const opt = document.createElement('option'); opt.value = v; opt.text = v; opt.selected = true; select.add(opt);
      const badgesWrap = document.getElementById('categoriaBadges');
      if (badgesWrap) {
        const btn = document.createElement('button'); btn.type='button'; btn.className='btn btn-sm btn-outline-dark me-1 mb-1 category-badge'; btn.dataset.cat = v; btn.textContent = v;
        badgesWrap.appendChild(btn);
      }
      newInput.value='';
      const newArea = document.getElementById('newCatArea'); if (newArea) newArea.style.display='none';
      return;
    }

    // Category badge click (delegated)
    const badge = e.target.closest('.category-badge');
    if (badge) {
      const select = document.getElementById('selectCategoria');
      if (!select) return;
      const c = badge.dataset.cat; if (!c) return;
      // select matching option case-insensitive
      for (let i=0;i<select.options.length;i++){ if (select.options[i].value.toLowerCase() === c.toLowerCase()){ select.value = select.options[i].value; break; } }
      badge.classList.add('btn-primary'); setTimeout(()=> badge.classList.remove('btn-primary'), 350);
      return;
    }
  });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
