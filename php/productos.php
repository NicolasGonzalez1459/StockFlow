<?php
require 'config.php';
$u = requiere(['admin','vendedor','repositor','cliente']);
$a = $_GET['a'] ?? 'listar';
$d = body();

if($a === 'listar'){
  if($u['rol'] === 'cliente'){
    $sql = 'SELECT p.id,p.codigo,p.nombre,p.categoria_id,p.unidad,p.precio_venta,p.stock,p.imagen,c.nombre AS categoria FROM productos p LEFT JOIN categorias_producto c ON c.id=p.categoria_id WHERE p.activo=1 ORDER BY p.nombre';
  }else{
    $sql = 'SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias_producto c ON c.id=p.categoria_id WHERE p.activo=1 ORDER BY p.nombre';
  }
  out(['items'=>db()->query($sql)->fetchAll()]);
}

if($a === 'categorias'){
  out(['items'=>db()->query('SELECT * FROM categorias_producto ORDER BY nombre')->fetchAll()]);
}

if($a === 'crear'){
  requiere(['admin']);
  try{
    $st = db()->prepare('INSERT INTO productos (codigo,nombre,categoria_id,unidad,precio_costo,precio_venta,stock,stock_minimo) VALUES (?,?,?,?,?,?,?,?)');
    $st->execute([$d['codigo'],$d['nombre'],$d['categoria_id'] ?: null,$d['unidad'],$d['precio_costo'],$d['precio_venta'],(int)$d['stock'],(int)$d['stock_minimo']]);
  }catch(PDOException $e){
    if($e->getCode() === '23000') out(['error'=>'codigo_duplicado'], 400);
    out(['error'=>'db'], 500);
  }
  $id = db()->lastInsertId();
  if((int)$d['stock'] > 0){
    $m = db()->prepare('INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo) VALUES (?,?,?,?,?,?)');
    $m->execute([$id,$u['id'],'ingreso',(int)$d['stock'],(int)$d['stock'],'Alta de producto']);
  }
  revisarStock($id);
  out(['id'=>$id]);
}

if($a === 'editar'){
  requiere(['admin']);
  try{
    $st = db()->prepare('UPDATE productos SET codigo=?,nombre=?,categoria_id=?,unidad=?,precio_costo=?,precio_venta=?,stock_minimo=? WHERE id=?');
    $st->execute([$d['codigo'],$d['nombre'],$d['categoria_id'] ?: null,$d['unidad'],$d['precio_costo'],$d['precio_venta'],(int)$d['stock_minimo'],(int)$d['id']]);
  }catch(PDOException $e){
    if($e->getCode() === '23000') out(['error'=>'codigo_duplicado'], 400);
    out(['error'=>'db'], 500);
  }
  revisarStock((int)$d['id']);
  out(['ok'=>true]);
}

if($a === 'imagen'){
  requiere(['admin']);
  $id = (int)($_POST['id'] ?? 0);
  $st = db()->prepare('SELECT id FROM productos WHERE id=?');
  $st->execute([$id]);
  if(!$id || !$st->fetch()) out(['error'=>'producto'], 404);
  if(empty($_FILES['imagen']['name']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) out(['error'=>'archivo'], 400);
  $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
  if(!in_array($ext, ['jpg','jpeg','png','webp'])) out(['error'=>'formato'], 400);
  if($_FILES['imagen']['size'] > 3 * 1024 * 1024) out(['error'=>'tamano'], 400);
  $dir = __DIR__ . '/../img/productos';
  if(!is_dir($dir)) mkdir($dir, 0755, true);
  $nombre = 'p' . $id . '_' . time() . '.' . $ext;
  if(!move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . '/' . $nombre)) out(['error'=>'subida'], 500);
  $ruta = 'img/productos/' . $nombre;
  db()->prepare('UPDATE productos SET imagen=? WHERE id=?')->execute([$ruta, $id]);
  out(['imagen'=>$ruta]);
}

if($a === 'eliminar'){
  requiere(['admin']);
  $st = db()->prepare('UPDATE productos SET activo=0 WHERE id=?');
  $st->execute([(int)$d['id']]);
  out(['ok'=>true]);
}

if($a === 'movimiento'){
  requiere(['admin','repositor']);
  $cant = (int)$d['cantidad'];
  $tipo = $d['tipo'];
  $st = db()->prepare('SELECT stock FROM productos WHERE id=?');
  $st->execute([(int)$d['producto_id']]);
  $p = $st->fetch();
  if(!$p) out(['error'=>'producto'], 404);
  $nuevo = $tipo === 'ingreso' ? $p['stock'] + $cant : ($tipo === 'egreso' ? $p['stock'] - $cant : $cant);
  if($nuevo < 0) out(['error'=>'stock_insuficiente'], 400);
  db()->prepare('UPDATE productos SET stock=? WHERE id=?')->execute([$nuevo,(int)$d['producto_id']]);
  db()->prepare('INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo) VALUES (?,?,?,?,?,?)')
    ->execute([(int)$d['producto_id'],$u['id'],$tipo,$cant,$nuevo,$d['motivo'] ?? '']);
  revisarStock((int)$d['producto_id']);
  out(['stock'=>$nuevo]);
}

if($a === 'movimientos'){
  $sql = 'SELECT m.*, p.nombre AS producto, u.nombre AS usuario FROM movimientos_stock m LEFT JOIN productos p ON p.id=m.producto_id LEFT JOIN usuarios u ON u.id=m.usuario_id ORDER BY m.fecha DESC LIMIT 200';
  out(['items'=>db()->query($sql)->fetchAll()]);
}

if($a === 'alertas'){
  out(['items'=>db()->query('SELECT id,codigo,nombre,stock,stock_minimo FROM productos WHERE activo=1 AND stock<=stock_minimo ORDER BY stock')->fetchAll()]);
}

out(['error'=>'accion'], 400);
