<?php
require __DIR__ . '/config/config.php';
$usuario = Sesion::requiere(['admin']);

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        Gasto::crear(
            $_POST['categoria_id'] ?: null,
            $usuario['id'],
            trim($_POST['descripcion']),
            (float) $_POST['monto'],
            $_POST['fecha']
        );
        $mensaje = 'Gasto registrado correctamente';
    }

    if ($accion === 'eliminar') {
        Gasto::eliminar((int) $_POST['id']);
        $mensaje = 'Gasto eliminado';
    }
}

$gastos = Gasto::listar();
$categorias = CategoriaGasto::listarTodas();

$tituloPagina = 'Gastos';
$paginaActual = 'gastos';
require 'parciales/cabecera.php';
?>

<div class="panel">
  <h2>Nuevo gasto</h2>
  <form method="post" class="row">
    <input type="hidden" name="accion" value="crear">
    <div>
      <label>Categoria</label>
      <select name="categoria_id">
        <option value="">Sin categoria</option>
        <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label>Descripcion</label><input name="descripcion" required></div>
    <div><label>Monto</label><input type="number" step="0.01" name="monto" required></div>
    <div><label>Fecha</label><input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required></div>
    <div style="flex:0"><button class="btn">Guardar</button></div>
  </form>
</div>

<div class="panel">
  <h2>Gastos</h2>
  <table>
    <thead><tr><th>Fecha</th><th>Categoria</th><th>Descripcion</th><th>Monto</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach ($gastos as $g): ?>
      <tr>
        <td><?= $g['fecha'] ?></td>
        <td><?= htmlspecialchars($g['categoria'] ?? '-') ?></td>
        <td><?= htmlspecialchars($g['descripcion']) ?></td>
        <td>$<?= number_format($g['monto'], 2) ?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="accion" value="eliminar">
            <input type="hidden" name="id" value="<?= $g['id'] ?>">
            <button class="btn sec">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$gastos): ?><tr><td colspan="5">Sin gastos registrados</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require 'parciales/pie.php'; ?>
