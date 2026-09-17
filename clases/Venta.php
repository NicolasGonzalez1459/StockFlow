<?php
class Venta
{
    public static function listar(array $usuario): array
    {
        return VentaDAO::listar($usuario);
    }

    public static function buscarPorId(int $id): ?array
    {
        return VentaDAO::buscarPorId($id);
    }

    public static function detalle(int $ventaId): array
    {
        return VentaDAO::detalle($ventaId);
    }

    public static function puedeVerCliente(int $ventaId, string $nombreCliente): bool
    {
        $st = Conexion::obtener()->prepare('SELECT id FROM ventas WHERE id=? AND cliente=?');
        $st->execute([$ventaId, $nombreCliente]);
        return (bool) $st->fetch();
    }

    public static function crear(array $usuario, array $items, string $clienteManual = '', string $canalManual = 'web'): array
    {
        if (!$items) return ['ok' => false, 'error' => 'sin_items'];
        $cliente = $usuario['rol'] === 'cliente' ? $usuario['nombre'] : $clienteManual;
        $canal = $usuario['rol'] === 'cliente' ? 'web' : $canalManual;
        $pdo = Conexion::obtener();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('INSERT INTO ventas (usuario_id,cliente,canal,total,costo_total) VALUES (?,?,?,0,0)')
                ->execute([$usuario['id'], $cliente, $canal]);
            $ventaId = (int) $pdo->lastInsertId();
            $total = 0;
            $costo = 0;
            foreach ($items as $it) {
                $st = $pdo->prepare('SELECT * FROM productos WHERE id=? FOR UPDATE');
                $st->execute([(int) $it['producto_id']]);
                $p = $st->fetch();
                $cant = (int) $it['cantidad'];
                if (!$p || $p['stock'] < $cant) throw new Exception('stock_insuficiente');
                $sub = $p['precio_venta'] * $cant;
                $total += $sub;
                $costo += $p['precio_costo'] * $cant;
                $pdo->prepare('INSERT INTO venta_detalle (venta_id,producto_id,cantidad,precio_unitario,costo_unitario,subtotal) VALUES (?,?,?,?,?,?)')
                    ->execute([$ventaId, $p['id'], $cant, $p['precio_venta'], $p['precio_costo'], $sub]);
                $nuevo = $p['stock'] - $cant;
                $pdo->prepare('UPDATE productos SET stock=? WHERE id=?')->execute([$nuevo, $p['id']]);
                MovimientoStock::registrar($p['id'], $usuario['id'], 'egreso', $cant, $nuevo, 'Venta #' . $ventaId);
            }
            $pdo->prepare('UPDATE ventas SET total=?,costo_total=? WHERE id=?')->execute([$total, $costo, $ventaId]);
            $pdo->commit();
            Notificacion::crear('venta', 'Nueva venta #' . $ventaId . ' por $' . number_format($total, 2));
            foreach ($items as $it) Producto::revisarStock((int) $it['producto_id']);
            return ['ok' => true, 'id' => $ventaId, 'total' => $total];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public static function cambiarEstado(int $id, string $estado): void
    {
        Conexion::obtener()->prepare('UPDATE ventas SET estado=? WHERE id=?')->execute([$estado, $id]);
    }
}
