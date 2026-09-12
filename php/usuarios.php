<?php
require 'config.php';
requiere(['admin']);
$a = $_GET['a'] ?? 'listar';
$d = body();

if($a === 'listar'){
  out(['items'=>db()->query('SELECT id,nombre,email,rol,activo,creado FROM usuarios ORDER BY id')->fetchAll()]);
}

if($a === 'crear'){
  $st = db()->prepare('INSERT INTO usuarios (nombre,email,password,rol) VALUES (?,?,?,?)');
  $st->execute([$d['nombre'], $d['email'], password_hash($d['password'], PASSWORD_DEFAULT), $d['rol']]);
  out(['id'=>db()->lastInsertId()]);
}

if($a === 'editar'){
  $st = db()->prepare('UPDATE usuarios SET nombre=?,email=?,rol=?,activo=? WHERE id=?');
  $st->execute([$d['nombre'], $d['email'], $d['rol'], (int)$d['activo'], (int)$d['id']]);
  if(!empty($d['password'])){
    $p = db()->prepare('UPDATE usuarios SET password=? WHERE id=?');
    $p->execute([password_hash($d['password'], PASSWORD_DEFAULT), (int)$d['id']]);
  }
  out(['ok'=>true]);
}

if($a === 'eliminar'){
  $st = db()->prepare('UPDATE usuarios SET activo=0 WHERE id=?');
  $st->execute([(int)$d['id']]);
  out(['ok'=>true]);
}

out(['error'=>'accion'], 400);
