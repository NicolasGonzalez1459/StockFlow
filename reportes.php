<?php
require __DIR__ . '/config/config.php';
$usuario = Sesion::requiere(['admin']);

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');
$vista = $_GET['vista'] ?? 'ventas';

$ventas = $vista === 'ventas' ? Reporte::ventas($desde, $hasta) : [];
$stock = $vista === 'stock' ? Reporte::stock() : [];
$gastos = $vista === 'gastos' ? Reporte::gastos($desde, $hasta) : [];
$rentabilidad = $vista === 'rentabilidad' ? Reporte::rentabilidad($desde, $hasta) : [];

$tituloPagina = 'Reportes';
$paginaActual = 'reportes';
require 'parciales/cabecera.php';
?>

<div class="panel row">
  <form method="get" class="row" style="flex:1">
    <div><label>Desde</label><input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>"></div>
    <div><label>Hasta</label><input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>"></div>
    <div>
      <label>Reporte</label>
      <select name="vista">
        <option value="ventas" <?= $vista === 'ventas' ? 'selected' : '' ?>>Ventas</option>
        <option value="stock" <?= $vista === 'stock' ? 'selected' : '' ?>>Stock</option>
        <option value="gastos" <?= $vista === 'gastos' ? 'selected' : '' ?>>Gastos</option>
        <option value="rentabilidad" <?= $vista === 'rentabilidad' ? 'selected' : '' ?>>Rentabilidad</option>
      </select>
    </div>
    <div style="flex:0"><button class="btn">Generar</button></div>
  </form>
</div>

<?php if ($vista === 'ventas'): ?>
<div class="panel">
  <h2>Reporte de ventas</h2>
  <table>
    <thead><tr><th>ID</th><th>Fecha</th><th>Canal</th><th>Cliente</th><th>Total</th><th>Estado</th><th>Vendedor</th></tr></thead>
    <tbody>
      <?php foreach ($ventas as $v): ?>
      <tr>
        <td><?= $v['id'] ?></td><td><?= substr($v['fecha'], 0, 16) ?></td><td><?= htmlspecialchars($v['canal']) ?></td>
        <td><?= htmlspecialchars($v['cliente'] ?? '-') ?></td><td>$<?= number_format($v['total'], 2) ?></td>
        <td><?= htmlspecialchars($v['estado']) ?></td><td><?= htmlspecialchars($v['vendedor'] ?? '-') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$ventas): ?><tr><td colspan="7">Sin datos</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if ($vista === 'stock'): ?>
<div class="panel">
  <h2>Reporte de stock</h2>
  <table>
    <thead><tr><th>Codigo</th><th>Nombre</th><th>Stock</th><th>Minimo</th><th>Costo</th><th>Venta</th><th>Valorizado</th></tr></thead>
    <tbody>
      <?php foreach ($stock as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['codigo']) ?></td><td><?= htmlspecialchars($s['nombre']) ?></td>
        <td><?= $s['stock'] ?></td><td><?= $s['stock_minimo'] ?></td>
        <td>$<?= number_format($s['precio_costo'], 2) ?></td><td>$<?= number_format($s['precio_venta'], 2) ?></td>
        <td>$<?= number_format($s['valorizado'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if ($vista === 'gastos'): ?>
<div class="panel">
  <h2>Reporte de gastos</h2>
  <table>
    <thead><tr><th>Fecha</th><th>Categoria</th><th>Descripcion</th><th>Monto</th></tr></thead>
    <tbody>
      <?php foreach ($gastos as $g): ?>
      <tr><td><?= $g['fecha'] ?></td><td><?= htmlspecialchars($g['categoria'] ?? '-') ?></td><td><?= htmlspecialchars($g['descripcion']) ?></td><td>$<?= number_format($g['monto'], 2) ?></td></tr>
      <?php endforeach; ?>
      <?php if (!$gastos): ?><tr><td colspan="4">Sin datos</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if ($vista === 'rentabilidad'): ?>
<div class="panel">
  <h2>Rentabilidad por producto</h2>
  <table>
    <thead><tr><th>Producto</th><th>Unidades</th><th>Ingresos</th><th>Costos</th><th>Margen</th></tr></thead>
    <tbody>
      <?php foreach ($rentabilidad as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['nombre'] ?? '-') ?></td><td><?= $r['unidades'] ?></td>
        <td>$<?= number_format($r['ingresos'], 2) ?></td><td>$<?= number_format($r['costos'], 2) ?></td>
        <td>$<?= number_format($r['margen'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$rentabilidad): ?><tr><td colspan="5">Sin datos</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require 'parciales/pie.php'; ?>
