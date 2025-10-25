<?php
require_once __DIR__ . '/../config/conexion.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$db = $conexion_global->getPdo();
$productos = $db->query('SELECT id_producto, nombre, precio_venta, stock FROM productos ORDER BY nombre')->fetchAll();
$clientes = $db->query('SELECT id_cliente, nombre FROM clientes ORDER BY nombre')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Registrar Venta</h2>
<?php require_once __DIR__ . '/../config/csrf.php'; ?>
<form id="ventaForm" action="/Ferreteria/controllers/ventasController.php" method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <div class="row mb-3">
    <div class="col-md-6">
      <label>Nombre cliente</label>
      <input type="text" name="cliente_nombre" class="form-control" placeholder="Nombre y apellidos" required>
    </div>
    <div class="col-md-3">
      <label>DNI / NIT</label>
      <input type="text" name="cliente_dni" class="form-control" placeholder="DNI / NIT">
    </div>
    <div class="col-md-3">
      <label>Teléfono</label>
      <input type="text" name="cliente_telefono" class="form-control" placeholder="Celular / teléfono">
    </div>
    <div class="col-12 mt-2">
      <label>Dirección</label>
      <input type="text" name="cliente_direccion" class="form-control" placeholder="Dirección (opcional)">
    </div>
  </div>

  <div class="mb-3">
    <label>Productos</label>
    <table class="table" id="tablaProductos">
      <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
      <tbody></tbody>
    </table>
    <button type="button" id="agregarProducto" class="btn btn-sm btn-outline-primary">Agregar producto</button>
  </div>

  <div class="mb-3 text-end">
    <h4>Total: $<span id="total">0.00</span></h4>
  </div>

  <div class="d-grid">
    <button class="btn btn-success">Registrar Venta</button>
  </div>
</form>

<script>
const productos = <?= json_encode($productos) ?>;
function formato(n){return parseFloat(n).toFixed(2)}
function recalcular(){
  let total = 0;
  document.querySelectorAll('#tablaProductos tbody tr').forEach(function(row){
    const precio = parseFloat(row.querySelector('.precio').value||0);
    const cant = parseInt(row.querySelector('.cantidad').value||0);
    const sub = precio * cant;
    row.querySelector('.subtotal').textContent = formato(sub);
    total += sub;
  });
  document.getElementById('total').textContent = formato(total);
}

document.getElementById('agregarProducto').addEventListener('click', function(){
  const tbody = document.querySelector('#tablaProductos tbody');
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>
      <select name="productos[]" class="form-select producto-select">
        <option value="">-- seleccionar --</option>
        ${productos.map(p=>`<option value="${p.id_producto}" data-precio="${p.precio_venta}" data-stock="${p.stock}">${p.nombre}</option>`).join('')}
      </select>
    </td>
    <td><input type="hidden" class="precio" name="precios[]" value="0"><span class="precio_text">0.00</span></td>
    <td><input type="number" name="cantidades[]" class="form-control cantidad" value="1" min="1"></td>
    <td>$<span class="subtotal">0.00</span></td>
    <td><button type="button" class="btn btn-sm btn-danger quitar">X</button></td>
  `;
  tbody.appendChild(tr);

  tr.querySelector('.producto-select').addEventListener('change', function(){
    const opt = this.selectedOptions[0];
    const precio = opt ? opt.getAttribute('data-precio') : 0;
    tr.querySelector('.precio').value = precio;
    tr.querySelector('.precio_text').textContent = parseFloat(precio||0).toFixed(2);
    recalcular();
  });
  tr.querySelector('.cantidad').addEventListener('input', recalcular);
  tr.querySelector('.quitar').addEventListener('click', function(){ tr.remove(); recalcular(); });
});

document.getElementById('ventaForm').addEventListener('submit', function(e){
  // Validación simple
  if (document.querySelectorAll('#tablaProductos tbody tr').length === 0) {
    e.preventDefault(); Swal.fire('Error','Agrega al menos un producto','error');
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
