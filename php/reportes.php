<?php
require 'config.php';
requiere(['admin']);
$a = $_GET['a'] ?? 'dashboard';
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

if($a === 'dashboard'){
  $v = db()->prepare('SELECT COUNT(*) c, COALESCE(SUM(total),0) t, COALESCE(SUM(costo_total),0) k FROM ventas WHERE DATE(fecha) BETWEEN ? AND ?');
  $v->execute([$desde,$hasta]);
  $ventas = $v->fetch();
  $g = db()->prepare('SELECT COALESCE(SUM(monto),0) g FROM gastos WHERE fecha BETWEEN ? AND ?');
  $g->execute([$desde,$hasta]);
  $gastos = $g->fetch()['g'];
  $top = db()->prepare('SELECT p.nombre, SUM(vd.cantidad) cant, SUM(vd.subtotal) total FROM venta_detalle vd JOIN ventas v ON v.id=vd.venta_id LEFT JOIN productos p ON p.id=vd.producto_id WHERE DATE(v.fecha) BETWEEN ? AND ? GROUP BY vd.producto_id ORDER BY cant DESC LIMIT 5');
  $top->execute([$desde,$hasta]);
  $serie = db()->prepare('SELECT DATE(fecha) dia, SUM(total) total FROM ventas WHERE DATE(fecha) BETWEEN ? AND ? GROUP BY DATE(fecha) ORDER BY dia');
  $serie->execute([$desde,$hasta]);
  $criticos = db()->query('SELECT COUNT(*) c FROM productos WHERE activo=1 AND stock<=stock_minimo')->fetch()['c'];
  out([
    'ventas_cantidad'=>(int)$ventas['c'],
    'ventas_total'=>(float)$ventas['t'],
    'costo_total'=>(float)$ventas['k'],
    'gastos_total'=>(float)$gastos,
    'margen'=>(float)$ventas['t'] - (float)$ventas['k'] - (float)$gastos,
    'stock_critico'=>(int)$criticos,
    'top'=>$top->fetchAll(),
    'serie'=>$serie->fetchAll()
  ]);
}

if($a === 'ventas'){
  $st = db()->prepare('SELECT v.id, v.fecha, v.canal, v.cliente, v.total, v.estado, u.nombre vendedor FROM ventas v LEFT JOIN usuarios u ON u.id=v.usuario_id WHERE DATE(v.fecha) BETWEEN ? AND ? ORDER BY v.fecha');
  $st->execute([$desde,$hasta]);
  out(['items'=>$st->fetchAll()]);
}

if($a === 'stock'){
  out(['items'=>db()->query('SELECT codigo,nombre,stock,stock_minimo,precio_costo,precio_venta,(stock*precio_costo) valorizado FROM productos WHERE activo=1 ORDER BY nombre')->fetchAll()]);
}

if($a === 'gastos'){
  $st = db()->prepare('SELECT g.fecha, c.nombre categoria, g.descripcion, g.monto FROM gastos g LEFT JOIN categorias_gasto c ON c.id=g.categoria_id WHERE g.fecha BETWEEN ? AND ? ORDER BY g.fecha');
  $st->execute([$desde,$hasta]);
  out(['items'=>$st->fetchAll()]);
}

if($a === 'rentabilidad'){
  $st = db()->prepare('SELECT p.nombre, SUM(vd.cantidad) unidades, SUM(vd.subtotal) ingresos, SUM(vd.cantidad*vd.costo_unitario) costos, SUM(vd.subtotal - vd.cantidad*vd.costo_unitario) margen FROM venta_detalle vd JOIN ventas v ON v.id=vd.venta_id LEFT JOIN productos p ON p.id=vd.producto_id WHERE DATE(v.fecha) BETWEEN ? AND ? GROUP BY vd.producto_id ORDER BY margen DESC');
  $st->execute([$desde,$hasta]);
  out(['items'=>$st->fetchAll()]);
}

out(['error'=>'accion'], 400);
