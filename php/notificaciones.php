<?php
require 'config.php';
requiere(['admin','vendedor','repositor']);
$a = $_GET['a'] ?? 'listar';
$d = body();

if($a === 'listar'){
  out(['items'=>db()->query('SELECT * FROM notificaciones ORDER BY fecha DESC LIMIT 30')->fetchAll()]);
}

if($a === 'leer'){
  db()->prepare('UPDATE notificaciones SET leida=1 WHERE id=?')->execute([(int)$d['id']]);
  out(['ok'=>true]);
}

out(['error'=>'accion'], 400);
