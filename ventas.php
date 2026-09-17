<?php
require __DIR__ . '/config/config.php';
$usuario = Sesion::requiere(['admin', 'vendedor']);

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $items = [];
        $prods = $_POST['producto_id'] ?? [];
        $cants = $_POST['cantidad'] ?? [];
        foreach ($prods as $i => $pid) {
            $cant = (int) ($cants[$i] ?? 0);
            if ($pid !== '' && $cant > 0) $items[] = ['producto_id' => (int) $pid, 'cantidad' => $cant];
        }
        $r = Venta::crear($usuario, $items, trim($_POST['cliente'] ?? ''), trim($_POST['canal'] ?? 'web'));
        if ($r['ok']) $mensaje = 'Venta #' . $r['id'] . ' registrada por $' . number_format($r['total'], 2);
        else $error = 'No se pudo registrar la venta (' . $r['error'] . ')';
    }

    if ($accion === 'estado' && $usuario['rol'] === 'admin') {
        Venta::cambiarEstado((int) $_POST['id'], $_POST['estado']);
        $mensaje = 'Estado actualizado';
    }
}

$productos = Producto::listarActivos(true);
$ventas = Venta::listar($usuario);
$verDetalleDe = isset($_GET['detalle']) ? (int) $_GET['detalle'] : null;
$detalleItems = $verDetalleDe ? Venta::detalle($verDetalleDe) : [];

$tituloPagina = 'Ventas';
$paginaActual = 'ventas';
require 'parciales/cabecera.php';
?>

<div class="panel">
  <h2>Nueva venta</h2>
  <form method="post">
    <input type="hidden" name="accion" value="crear">
    <div class="row">
      <div><label>Cliente</label><input name="cliente" placeholder="Nombre del cliente"></div>
      <div>
        <label>Canal</label>
        <select name="canal">
          <option value="tienda_online">Tienda online</option>
          <option value="marketplace">Marketplace</option>
          <option value="redes">Redes sociales</option>
          <option value="web">Web</option>
        </select>
      </div>
    </div>
    <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="row">
      <div>
        <label>Producto <?= $i + 1 ?></label>
        <select name="producto_id[]">
          <option value="">-- No seleccionar --</option>
          <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id'] ?>">
            <?= htmlspecialchars($p['nombre']) ?> ($<?= number_format($p['precio_venta'], 2) ?>, stock: <?= $p['stock'] ?>)
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label>Cantidad</label><input type="number" name="cantidad[]" min="0" value="0"></div>
    </div>
    <?php endfor; ?>
    <div style="margin-top:10px"><button class="btn">Registrar venta</button></div>
  </form>
</div>

<div class="panel">
  <h2>Ventas</h2>
  <table>
    <thead><tr><th>ID</th><th>Fecha</th><th>Cliente</th><th>Canal</th><th>Total</th><th>Estado</th><th>Vendedor</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach ($ventas as $v): ?>
      <tr>
        <td><?= $v['id'] ?></td>
        <td><?= substr($v['fecha'], 0, 16) ?></td>
        <td><?= htmlspecialchars($v['cliente'] ?? '-') ?></td>
        <td><?= htmlspecialchars($v['canal']) ?></td>
        <td>$<?= number_format($v['total'], 2) ?></td>
        <td>
          <?php if ($usuario['rol'] === 'admin'): ?>
          <form method="post" style="display:flex;gap:4px">
            <input type="hidden" name="accion" value="estado">
            <input type="hidden" name="id" value="<?= $v['id'] ?>">
            <select name="estado">
              <?php foreach (['pendiente', 'enviado', 'entregado', 'cancelado'] as $e): ?>
              <option value="<?= $e ?>" <?= $v['estado'] === $e ? 'selected' : '' ?>><?= $e ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn sec">Guardar</button>
          </form>
          <?php else: ?>
          <?= htmlspecialchars($v['estado']) ?>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($v['vendedor'] ?? '-') ?></td>
        <td><a class="btn sec" href="ventas.php?detalle=<?= $v['id'] ?>">Ver</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$ventas): ?><tr><td colspan="8">Sin ventas registradas</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($verDetalleDe): ?>
<div class="panel">
  <h2>Detalle de la venta #<?= $verDetalleDe ?></h2>
  <table>
    <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th></tr></thead>
    <tbody>
      <?php foreach ($detalleItems as $it): ?>
      <tr>
        <td><?= htmlspecialchars($it['producto'] ?? '-') ?></td>
        <td><?= $it['cantidad'] ?></td>
        <td>$<?= number_format($it['precio_unitario'], 2) ?></td>
        <td>$<?= number_format($it['subtotal'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require 'parciales/pie.php'; ?>
