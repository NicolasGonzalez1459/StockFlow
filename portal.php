<?php
require __DIR__ . '/config/config.php';
$usuario = Sesion::requiere(['cliente', 'admin']);

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $pid = (int) $_POST['producto_id'];
        $cant = max(1, (int) $_POST['cantidad']);
        $_SESSION['carrito'][$pid] = ($_SESSION['carrito'][$pid] ?? 0) + $cant;
        $mensaje = 'Producto agregado al carrito';
    }

    if ($accion === 'quitar') {
        unset($_SESSION['carrito'][(int) $_POST['producto_id']]);
        $mensaje = 'Producto quitado del carrito';
    }

    if ($accion === 'vaciar') {
        $_SESSION['carrito'] = [];
        $mensaje = 'Carrito vaciado';
    }

    if ($accion === 'confirmar') {
        $items = [];
        foreach ($_SESSION['carrito'] as $pid => $cant) {
            $items[] = ['producto_id' => (int) $pid, 'cantidad' => (int) $cant];
        }
        $r = Venta::crear($usuario, $items);
        if ($r['ok']) {
            $_SESSION['carrito'] = [];
            $mensaje = 'Compra confirmada. Pedido #' . $r['id'] . ' por $' . number_format($r['total'], 2);
        } else {
            $error = 'No se pudo confirmar la compra (' . $r['error'] . ')';
        }
    }
}

$productos = Producto::listarActivos(true);
$productosPorId = [];
foreach ($productos as $p) $productosPorId[$p['id']] = $p;

$pedidos = Venta::listar($usuario);

$tituloPagina = 'Portal del cliente';
$paginaActual = 'portal';
require 'parciales/cabecera.php';
?>

<div class="panel">
  <h2>Catalogo</h2>
  <table>
    <thead><tr><th>Foto</th><th>Producto</th><th>Categoria</th><th>Precio</th><th>Stock</th><th>Agregar</th></tr></thead>
    <tbody>
      <?php foreach ($productos as $p): ?>
      <tr>
        <td><?php if (!empty($p['imagen'])): ?><img src="<?= htmlspecialchars($p['imagen']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px"><?php endif; ?></td>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
        <td>$<?= number_format($p['precio_venta'], 2) ?></td>
        <td><?= $p['stock'] ?></td>
        <td>
          <form method="post" style="display:flex;gap:4px">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
            <input type="number" name="cantidad" value="1" min="1" max="<?= $p['stock'] ?>" style="width:70px">
            <button class="btn">Agregar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="panel">
  <h2>Carrito</h2>
  <table>
    <thead><tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
    <tbody>
      <?php $totalCarrito = 0; ?>
      <?php foreach ($_SESSION['carrito'] as $pid => $cant): ?>
        <?php if (!isset($productosPorId[$pid])) continue; ?>
        <?php $p = $productosPorId[$pid]; $sub = $p['precio_venta'] * $cant; $totalCarrito += $sub; ?>
        <tr>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= $cant ?></td>
          <td>$<?= number_format($sub, 2) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="accion" value="quitar">
              <input type="hidden" name="producto_id" value="<?= $pid ?>">
              <button class="btn sec">Quitar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$_SESSION['carrito']): ?><tr><td colspan="4">El carrito esta vacio</td></tr><?php endif; ?>
    </tbody>
  </table>
  <?php if ($_SESSION['carrito']): ?>
  <p style="margin-top:10px;font-weight:bold">Total: $<?= number_format($totalCarrito, 2) ?></p>
  <div style="display:flex;gap:8px;margin-top:8px">
    <form method="post"><input type="hidden" name="accion" value="confirmar"><button class="btn">Confirmar compra</button></form>
    <form method="post"><input type="hidden" name="accion" value="vaciar"><button class="btn sec">Vaciar carrito</button></form>
  </div>
  <?php endif; ?>
</div>

<div class="panel">
  <h2>Mis pedidos</h2>
  <table>
    <thead><tr><th>ID</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Canal</th></tr></thead>
    <tbody>
      <?php foreach ($pedidos as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td><td><?= substr($p['fecha'], 0, 16) ?></td>
        <td>$<?= number_format($p['total'], 2) ?></td><td><?= htmlspecialchars($p['estado']) ?></td><td><?= htmlspecialchars($p['canal']) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$pedidos): ?><tr><td colspan="5">Todavia no hiciste pedidos</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require 'parciales/pie.php'; ?>
