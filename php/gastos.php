<?php
require 'config.php';
$u = requiere(['admin']);
$a = $_GET['a'] ?? 'listar';
$d = body();

if($a === 'listar'){
  $sql = 'SELECT g.*, c.nombre AS categoria FROM gastos g LEFT JOIN categorias_gasto c ON c.id=g.categoria_id ORDER BY g.fecha DESC';
  out(['items'=>db()->query($sql)->fetchAll()]);
}

if($a === 'categorias'){
  out(['items'=>db()->query('SELECT * FROM categorias_gasto ORDER BY nombre')->fetchAll()]);
}

if($a === 'crear'){
  db()->prepare('INSERT INTO gastos (categoria_id,usuario_id,descripcion,monto,fecha) VALUES (?,?,?,?,?)')
    ->execute([$d['categoria_id'] ?: null,$u['id'],$d['descripcion'],$d['monto'],$d['fecha']]);
  out(['id'=>db()->lastInsertId()]);
}

if($a === 'eliminar'){
  db()->prepare('DELETE FROM gastos WHERE id=?')->execute([(int)$d['id']]);
  out(['ok'=>true]);
}

out(['error'=>'accion'], 400);
