<?php
require __DIR__ . '/config/config.php';
$usuario = Sesion::requiere(['admin', 'repositor', 'vendedor']);

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear' && $usuario['rol'] === 'admin') {
        $p = new Producto();
        $p->codigo = trim($_POST['codigo']);
        $p->nombre = trim($_POST['nombre']);
        $p->categoria_id = $_POST['categoria_id'] ?: null;
        $p->unidad = trim($_POST['unidad']) ?: 'unidad';
        $p->precio_costo = (float) $_POST['precio_costo'];
        $p->precio_venta = (float) $_POST['precio_venta'];
        $p->stock = (int) $_POST['stock'];
        $p->stock_minimo = (int) $_POST['stock_minimo'];
        $r = $p->crear($usuario['id']);
        if ($r['ok']) $mensaje = 'Producto creado correctamente';
        else $error = 'No se pudo crear el producto (' . $r['error'] . ')';
    }

    if ($accion === 'editar' && $usuario['rol'] === 'admin') {
        $p = new Producto();
        $p->id = (int) $_POST['id'];
        $p->codigo = trim($_POST['codigo']);
        $p->nombre = trim($_POST['nombre']);
        $p->categoria_id = $_POST['categoria_id'] ?: null;
        $p->unidad = trim($_POST['unidad']) ?: 'unidad';
        $p->precio_costo = (float) $_POST['precio_costo'];
        $p->precio_venta = (float) $_POST['precio_venta'];
        $p->stock_minimo = (int) $_POST['stock_minimo'];
        $r = $p->actualizar();
        if ($r['ok']) $mensaje = 'Producto actualizado correctamente';
        else $error = 'No se pudo actualizar el producto (' . $r['error'] . ')';
    }

    if ($accion === 'eliminar' && $usuario['rol'] === 'admin') {
        Producto::eliminar((int) $_POST['id']);
        $mensaje = 'Producto dado de baja';
    }

    if ($accion === 'imagen' && $usuario['rol'] === 'admin') {
        $r = Producto::guardarImagen((int) $_POST['id'], $_FILES['imagen'] ?? []);
        if ($r['ok']) $mensaje = 'Imagen actualizada';
        else $error = 'No se pudo subir la imagen (' . $r['error'] . ')';
    }

    if ($accion === 'movimiento' && in_array($usuario['rol'], ['admin', 'repositor'], true)) {
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
}

$productos = Producto::listarActivos();
$categorias = CategoriaProducto::listarTodas();
$alertas = Producto::alertasStock();
$editando = null;
if (isset($_GET['editar']) && $usuario['rol'] === 'admin') {
    $editando = Producto::buscarPorId((int) $_GET['editar']);
}

$tituloPagina = 'Inventario';
$paginaActual = 'inventario';
require 'parciales/cabecera.php';
?>

<?php if ($alertas): ?>
<div class="panel" style="border-left:4px solid var(--dan)">
  <h2>Alertas de stock minimo</h2>
  <table>
    <thead><tr><th>Codigo</th><th>Producto</th><th>Stock</th><th>Minimo</th></tr></thead>
    <tbody>
      <?php foreach ($alertas as $a): ?>
      <tr><td><?= htmlspecialchars($a['codigo']) ?></td><td><?= htmlspecialchars($a['nombre']) ?></td><td class="bad"><?= $a['stock'] ?></td><td><?= $a['stock_minimo'] ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php if ($usuario['rol'] === 'admin'): ?>
<div class="panel">
  <h2><?= $editando ? 'Editar producto' : 'Nuevo producto' ?></h2>
  <form method="post" class="row">
    <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'crear' ?>">
    <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
    <div><label>Codigo</label><input name="codigo" value="<?= htmlspecialchars($editando['codigo'] ?? '') ?>" required></div>
    <div><label>Nombre</label><input name="nombre" value="<?= htmlspecialchars($editando['nombre'] ?? '') ?>" required></div>
    <div>
      <label>Categoria</label>
      <select name="categoria_id">
        <option value="">Sin categoria</option>
        <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['id'] ?>" <?= (($editando['categoria_id'] ?? null) == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label>Unidad</label><input name="unidad" value="<?= htmlspecialchars($editando['unidad'] ?? 'unidad') ?>"></div>
    <div><label>Precio costo</label><input type="number" step="0.01" name="precio_costo" value="<?= htmlspecialchars($editando['precio_costo'] ?? '0') ?>" required></div>
    <div><label>Precio venta</label><input type="number" step="0.01" name="precio_venta" value="<?= htmlspecialchars($editando['precio_venta'] ?? '0') ?>" required></div>
    <?php if (!$editando): ?>
    <div><label>Stock inicial</label><input type="number" name="stock" value="0" required></div>
    <?php endif; ?>
    <div><label>Stock minimo</label><input type="number" name="stock_minimo" value="<?= htmlspecialchars($editando['stock_minimo'] ?? '0') ?>" required></div>
    <div style="flex:0"><button class="btn"><?= $editando ? 'Guardar cambios' : 'Crear' ?></button></div>
    <?php if ($editando): ?><div style="flex:0"><a class="btn sec" href="inventario.php">Cancelar</a></div><?php endif; ?>
  </form>
</div>
<?php endif; ?>

<?php if (in_array($usuario['rol'], ['admin', 'repositor'], true)): ?>
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
<?php endif; ?>

<div class="panel">
  <h2>Productos</h2>
  <table>
    <thead>
      <tr><th>Foto</th><th>Codigo</th><th>Nombre</th><th>Categoria</th><th>Precio venta</th><th>Stock</th><?php if ($usuario['rol'] === 'admin'): ?><th>Acciones</th><?php endif; ?></tr>
    </thead>
    <tbody>
      <?php foreach ($productos as $p): ?>
      <tr>
        <td><?php if (!empty($p['imagen'])): ?><img src="<?= htmlspecialchars($p['imagen']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px"><?php endif; ?></td>
        <td><?= htmlspecialchars($p['codigo']) ?></td>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
        <td>$<?= number_format($p['precio_venta'], 2) ?></td>
        <td class="<?= $p['stock'] <= $p['stock_minimo'] ? 'bad' : '' ?>"><?= $p['stock'] ?></td>
        <?php if ($usuario['rol'] === 'admin'): ?>
        <td>
          <a class="btn sec" href="inventario.php?editar=<?= $p['id'] ?>">Editar</a>
          <form method="post" style="display:inline">
            <input type="hidden" name="accion" value="eliminar">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button class="btn sec">Baja</button>
          </form>
          <details style="display:inline-block">
            <summary class="btn sec" style="display:inline-block;cursor:pointer">Foto</summary>
            <form method="post" enctype="multipart/form-data">
              <input type="hidden" name="accion" value="imagen">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp" required>
              <button class="btn">Subir</button>
            </form>
          </details>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require 'parciales/pie.php'; ?>
