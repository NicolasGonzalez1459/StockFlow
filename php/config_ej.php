<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_base_de_datos');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');

function db(){
  static $pdo = null;
  if($pdo === null){
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  }
  return $pdo;
}

function body(){
  $d = json_decode(file_get_contents('php://input'), true);
  return is_array($d) ? $d : [];
}

function out($data, $code = 200){
  http_response_code($code);
  echo json_encode($data);
  exit;
}

function usuario(){
  return $_SESSION['usuario'] ?? null;
}

function requiere($roles = []){
  $u = usuario();
  if(!$u) out(['error'=>'no_autenticado'], 401);
  if($roles && !in_array($u['rol'], $roles)) out(['error'=>'sin_permiso'], 403);
  return $u;
}

function notificar($tipo, $mensaje){
  $st = db()->prepare('INSERT INTO notificaciones (tipo,mensaje) VALUES (?,?)');
  $st->execute([$tipo, $mensaje]);
}

function revisarStock($producto_id){
  $st = db()->prepare('SELECT nombre,stock,stock_minimo FROM productos WHERE id=?');
  $st->execute([$producto_id]);
  $p = $st->fetch();
  if($p && $p['stock'] <= $p['stock_minimo']){
    notificar('stock_minimo', 'Stock minimo alcanzado: '.$p['nombre'].' ('.$p['stock'].')');
  }
}