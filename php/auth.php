<?php
require 'config.php';
$a = $_GET['a'] ?? 'sesion';

if($a === 'login'){
  $d = body();
  $st = db()->prepare('SELECT * FROM usuarios WHERE email=? AND activo=1');
  $st->execute([trim($d['email'] ?? '')]);
  $u = $st->fetch();
  if(!$u || !password_verify($d['password'] ?? '', $u['password'])) out(['error'=>'credenciales'], 401);
  $_SESSION['usuario'] = ['id'=>$u['id'],'nombre'=>$u['nombre'],'email'=>$u['email'],'rol'=>$u['rol']];
  out(['usuario'=>$_SESSION['usuario']]);
}

if($a === 'logout'){
  session_destroy();
  out(['ok'=>true]);
}

out(['usuario'=>usuario()]);
