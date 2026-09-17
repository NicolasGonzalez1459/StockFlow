<?php
require __DIR__ . '/config/config.php';
$usuario = Sesion::requiere(['admin', 'repositor']);

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'movimiento') {
    $r = Producto::registrarMovimiento(
        (int) $_POST['producto_id'],
        $_POST['tipo'],
        (int) $_POST['cantidad'],
        $usuario['id'],
        trim($_POST['motivo'] ?? '')
    );
    if ($r['ok']) $mensaje = 'Movimiento registrado. Stock actual: ' . $r['stock'];
    else $error = 'No se pudo registrar el movimiento (' . $r['error'] . ')';
}

$reporte = Reporte::dashboard($desde, $hasta);
$notificaciones = Notificacion::listar(10);
$productos = Producto::listarActivos();
$movimientos = MovimientoStock::listarUltimos(50);

$tituloPagina = 'Dashboard';
$paginaActual = 'dashboard';
require 'parciales/cabecera.php';
?>
<div class="panel row">
  <form method="get" class="row" style="flex:1">
    <div><label>Desde</label><input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>"></div>
    <div><label>Hasta</label><input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>"></div>
    <div style="flex:0"><button class="btn">Aplicar</button></div>
  </form>
</div>

<div class="cards">
  <div class="card"><div class="k">Ventas ($)</div><div class="v">$<?= number_format($reporte['ventas_total'], 2) ?></div></div>
  <div class="card"><div class="k">Cant. Ventas</div><div class="v"><?= $reporte['ventas_cantidad'] ?></div></div>
  <div class="card"><div class="k">Gastos ($)</div><div class="v">$<?= number_format($reporte['gastos_total'], 2) ?></div></div>
  <div class="card"><div class="k">Margen ($)</div><div class="v">$<?= number_format($reporte['margen'], 2) ?></div></div>
  <div class="card"><div class="k">Stock critico</div><div class="v bad"><?= $reporte['stock_critico'] ?></div></div>
</div>

<div class="panel">
  <h2>Productos mas vendidos</h2>
  <table>
    <thead><tr><th>Producto</th><th>Cantidad</th><th>Total</th></tr></thead>
    <tbody>
      <?php foreach ($reporte['top'] as $t): ?>
      <tr><td><?= htmlspecialchars($t['nombre'] ?? '-') ?></td><td><?= $t['cant'] ?></td><td>$<?= number_format($t['total'], 2) ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$reporte['top']): ?><tr><td colspan="3">Sin datos en el periodo</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="panel">
  <h2>Notificaciones</h2>
  <table>
    <tbody>
      <?php foreach ($notificaciones as $n): ?>
      <tr><td><?= substr($n['fecha'], 0, 16) ?></td><td><?= htmlspecialchars($n['mensaje']) ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$notificaciones): ?><tr><td>Sin notificaciones</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="panel">
  <h2>Registrar movimiento de stock</h2>
  <form method="post" class="row">
    <input type="hidden" name="accion" value="movimiento">
    <div>
      <label>Producto</label>
      <select name="producto_id" required>
        <?php foreach ($productos as $p): ?>
        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?> (stock: <?= $p['stock'] ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>Tipo</label>
      <select name="tipo">
        <option value="ingreso">Ingreso (+)</option>
        <option value="egreso">Egreso (-)</option>
        <option value="ajuste">Ajuste (=)</option>
      </select>
    </div>
    <div><label>Cantidad</label><input type="number" name="cantidad" min="1" value="1" required></div>
    <div><label>Motivo</label><input type="text" name="motivo"></div>
    <div style="flex:0"><button class="btn">Guardar</button></div>
  </form>
</div>

<div class="panel">
  <h2>Ultimos movimientos</h2>
  <table>
    <thead><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Stock resultante</th><th>Usuario</th></tr></thead>
    <tbody>
      <?php foreach ($movimientos as $m): ?>
      <tr>
        <td><?= substr($m['fecha'], 0, 16) ?></td>
        <td><?= htmlspecialchars($m['producto'] ?? '-') ?></td>
        <td><?= htmlspecialchars($m['tipo']) ?></td>
        <td><?= $m['cantidad'] ?></td>
        <td><?= $m['stock_resultante'] ?></td>
        <td><?= htmlspecialchars($m['usuario'] ?? '-') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require 'parciales/pie.php'; ?>
