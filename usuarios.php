<?php
require __DIR__ . '/config/config.php';
$usuario = Sesion::requiere(['admin']);

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $u = new Usuario();
        $u->nombre = trim($_POST['nombre']);
        $u->email = trim($_POST['email']);
        $u->password = $_POST['password'];
        $u->rol = $_POST['rol'];
        $u->crear();
        $mensaje = 'Usuario creado correctamente';
    }

    if ($accion === 'editar') {
        $u = new Usuario();
        $u->id = (int) $_POST['id'];
        $u->nombre = trim($_POST['nombre']);
        $u->email = trim($_POST['email']);
        $u->rol = $_POST['rol'];
        $u->activo = isset($_POST['activo']) ? 1 : 0;
        $u->password = $_POST['password'] ?? '';
        $u->actualizar();
        $mensaje = 'Usuario actualizado correctamente';
    }

    if ($accion === 'eliminar') {
        Usuario::eliminar((int) $_POST['id']);
        $mensaje = 'Usuario dado de baja';
    }
}

$usuarios = Usuario::listarTodos();
$editando = isset($_GET['editar']) ? Usuario::buscarPorId((int) $_GET['editar']) : null;

$tituloPagina = 'Usuarios';
$paginaActual = 'usuarios';
require 'parciales/cabecera.php';
?>

<div class="panel">
  <h2><?= $editando ? 'Editar usuario' : 'Nuevo usuario' ?></h2>
  <form method="post" class="row">
    <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'crear' ?>">
    <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
    <div><label>Nombre</label><input name="nombre" value="<?= htmlspecialchars($editando['nombre'] ?? '') ?>" required></div>
    <div><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($editando['email'] ?? '') ?>" required></div>
    <div>
      <label>Rol</label>
      <select name="rol">
        <?php foreach (['admin', 'vendedor', 'repositor', 'cliente'] as $r): ?>
        <option value="<?= $r ?>" <?= ($editando['rol'] ?? '') === $r ? 'selected' : '' ?>><?= $r ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label><?= $editando ? 'Nueva contrasena (opcional)' : 'Contrasena' ?></label><input type="password" name="password" <?= $editando ? '' : 'required' ?>></div>
    <?php if ($editando): ?>
    <div><label>Activo</label><input type="checkbox" name="activo" style="width:auto" <?= $editando['activo'] ? 'checked' : '' ?>></div>
    <?php endif; ?>
    <div style="flex:0"><button class="btn"><?= $editando ? 'Guardar cambios' : 'Crear' ?></button></div>
    <?php if ($editando): ?><div style="flex:0"><a class="btn sec" href="usuarios.php">Cancelar</a></div><?php endif; ?>
  </form>
</div>

<div class="panel">
  <h2>Usuarios</h2>
  <table>
    <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Activo</th><th>Creado</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['nombre']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['rol']) ?></td>
        <td><?= $u['activo'] ? 'Si' : 'No' ?></td>
        <td><?= substr($u['creado'], 0, 16) ?></td>
        <td>
          <a class="btn sec" href="usuarios.php?editar=<?= $u['id'] ?>">Editar</a>
          <form method="post" style="display:inline">
            <input type="hidden" name="accion" value="eliminar">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <button class="btn sec">Baja</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require 'parciales/pie.php'; ?>
