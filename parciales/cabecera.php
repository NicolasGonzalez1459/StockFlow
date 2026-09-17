<?php
$menu = [
    ['id' => 'dashboard', 'archivo' => 'dashboard.php', 'texto' => 'Dashboard', 'roles' => ['admin', 'repositor']],
    ['id' => 'inventario', 'archivo' => 'inventario.php', 'texto' => 'Inventario', 'roles' => ['admin', 'repositor', 'vendedor']],
    ['id' => 'ventas', 'archivo' => 'ventas.php', 'texto' => 'Ventas', 'roles' => ['admin', 'vendedor']],
    ['id' => 'gastos', 'archivo' => 'gastos.php', 'texto' => 'Gastos', 'roles' => ['admin']],
    ['id' => 'reportes', 'archivo' => 'reportes.php', 'texto' => 'Reportes', 'roles' => ['admin']],
    ['id' => 'usuarios', 'archivo' => 'usuarios.php', 'texto' => 'Usuarios', 'roles' => ['admin']],
    ['id' => 'portal', 'archivo' => 'portal.php', 'texto' => 'Portal', 'roles' => ['cliente', 'admin']]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($tituloPagina ?? 'StockFlow') ?> | StockFlow</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<div class="layout">
  <aside class="side">
    <div class="brand"><div class="logo">SF</div>Stock<span>Flow</span></div>
    <nav>
      <?php foreach ($menu as $m): if (!in_array($usuario['rol'], $m['roles'], true)) continue; ?>
      <a href="<?= $m['archivo'] ?>" class="<?= ($paginaActual ?? '') === $m['id'] ? 'on' : '' ?>"><?= $m['texto'] ?></a>
      <?php endforeach; ?>
    </nav>
  </aside>
  <main class="main">
    <div class="top">
      <div><h1><?= htmlspecialchars($tituloPagina ?? '') ?></h1><div class="h-sub">HuskyTech Solutions · BT Informatica 2026</div></div>
      <div class="top-right">
        <span class="chip"><?= htmlspecialchars($usuario['nombre'] . ' · ' . $usuario['rol']) ?></span>
        <a class="btn sec" href="logout.php">Salir</a>
      </div>
    </div>
    <?php if (!empty($mensaje)): ?><div class="panel" style="border-left:4px solid var(--acc)"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="panel" style="border-left:4px solid var(--dan);color:var(--dan)"><?= htmlspecialchars($error) ?></div><?php endif; ?>
