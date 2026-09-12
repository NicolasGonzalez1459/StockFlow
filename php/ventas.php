<?php
require 'config.php';
$u = requiere(['admin','vendedor','cliente']);
$a = $_GET['a'] ?? 'listar';
$d = body();

if($a === 'listar'){
  $sql = 'SELECT v.*, u.nombre AS vendedor FROM ventas v LEFT JOIN usuarios u ON u.id=v.usuario_id';
  $par = [];
  if($u['rol'] === 'vendedor'){ $sql .= ' WHERE v.usuario_id=?'; $par[] = $u['id']; }
  elseif($u['rol'] === 'cliente'){ $sql .= ' WHERE v.cliente=?'; $par[] = $u['nombre']; }
  $sql .= ' ORDER BY v.fecha DESC';
  $st = db()->prepare($sql);
  $st->execute($par);
  out(['items'=>$st->fetchAll()]);
}

if($a === 'detalle'){
  $vid = (int)($_GET['id'] ?? 0);
  if($u['rol'] === 'cliente'){
    $chk = db()->prepare('SELECT id FROM ventas WHERE id=? AND cliente=?');
    $chk->execute([$vid, $u['nombre']]);
    if(!$chk->fetch()) out(['error'=>'sin_permiso'], 403);
  }
  $st = db()->prepare('SELECT vd.*, p.nombre AS producto FROM venta_detalle vd LEFT JOIN productos p ON p.id=vd.producto_id WHERE vd.venta_id=?');
  $st->execute([$vid]);
  out(['items'=>$st->fetchAll()]);
}

if($a === 'crear'){
  requiere(['admin','vendedor','cliente']);
  $items = $d['items'] ?? [];
  if(!$items) out(['error'=>'sin_items'], 400);
  $cliente = $u['rol'] === 'cliente' ? $u['nombre'] : ($d['cliente'] ?? '');
  $canal = $u['rol'] === 'cliente' ? 'web' : ($d['canal'] ?? 'web');
  $pdo = db();
  $pdo->beginTransaction();
  try{
    $pdo->prepare('INSERT INTO ventas (usuario_id,cliente,canal,total,costo_total) VALUES (?,?,?,0,0)')
      ->execute([$u['id'], $cliente, $canal]);
    $vid = $pdo->lastInsertId();
    $total = 0; $costo = 0;
    foreach($items as $it){
      $st = $pdo->prepare('SELECT * FROM productos WHERE id=? FOR UPDATE');
      $st->execute([(int)$it['producto_id']]);
      $p = $st->fetch();
      $cant = (int)$it['cantidad'];
      if(!$p || $p['stock'] < $cant) throw new Exception('stock_insuficiente');
      $sub = $p['precio_venta'] * $cant;
      $total += $sub; $costo += $p['precio_costo'] * $cant;
      $pdo->prepare('INSERT INTO venta_detalle (venta_id,producto_id,cantidad,precio_unitario,costo_unitario,subtotal) VALUES (?,?,?,?,?,?)')
        ->execute([$vid,$p['id'],$cant,$p['precio_venta'],$p['precio_costo'],$sub]);
      $nuevo = $p['stock'] - $cant;
      $pdo->prepare('UPDATE productos SET stock=? WHERE id=?')->execute([$nuevo,$p['id']]);
      $pdo->prepare('INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo) VALUES (?,?,?,?,?,?)')
        ->execute([$p['id'],$u['id'],'egreso',$cant,$nuevo,'Venta #'.$vid]);
    }
    $pdo->prepare('UPDATE ventas SET total=?,costo_total=? WHERE id=?')->execute([$total,$costo,$vid]);
    $pdo->commit();
    notificar('venta','Nueva venta #'.$vid.' por $'.number_format($total,2));
    foreach($items as $it) revisarStock((int)$it['producto_id']);
    out(['id'=>$vid,'total'=>$total]);
  }catch(Exception $e){
    $pdo->rollBack();
    out(['error'=>$e->getMessage()], 400);
  }
}

if($a === 'estado'){
  requiere(['admin','vendedor']);
  db()->prepare('UPDATE ventas SET estado=? WHERE id=?')->execute([$d['estado'],(int)$d['id']]);
  out(['ok'=>true]);
}

out(['error'=>'accion'], 400);
