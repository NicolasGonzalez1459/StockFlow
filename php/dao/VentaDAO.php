<?php

final class VentaDAO extends DAO
{
    public const ESTADOS = ['pendiente', 'enviado', 'entregado', 'cancelado'];

    private function columnas(array $usuario): string
    {
        if ($usuario['rol'] === 'cliente') {
            return 'v.id,v.fecha,v.canal,v.total,v.estado';
        }
        return 'v.id,v.fecha,v.cliente,v.canal,v.total,v.estado,v.usuario_id,u.nombre AS vendedor';
    }

    private function alcance(array $usuario): array
    {
        if ($usuario['rol'] === 'vendedor') {
            return [' AND v.usuario_id=?', [$usuario['id']]];
        }
        if ($usuario['rol'] === 'cliente') {
            return [' AND v.cliente=?', [$usuario['nombre']]];
        }
        return ['', []];
    }

    public function listar(array $usuario): array
    {
        [$filtro, $params] = $this->alcance($usuario);
        return $this->consultar(
            'SELECT ' . $this->columnas($usuario) . ' FROM ventas v LEFT JOIN usuarios u ON u.id=v.usuario_id WHERE 1=1' . $filtro
            . ' ORDER BY v.fecha DESC, v.id DESC',
            $params
        );
    }

    public function obtener(int $id, array $usuario): ?array
    {
        [$filtro, $params] = $this->alcance($usuario);
        $venta = $this->fila(
            'SELECT ' . $this->columnas($usuario) . ' FROM ventas v LEFT JOIN usuarios u ON u.id=v.usuario_id WHERE v.id=?' . $filtro,
            array_merge([$id], $params)
        );
        if (!$venta) {
            return null;
        }
        $venta['items'] = $this->consultar(
            'SELECT vd.id,vd.producto_id,p.nombre AS producto,vd.cantidad,vd.precio_unitario,(vd.cantidad*vd.precio_unitario) AS subtotal
             FROM venta_detalle vd LEFT JOIN productos p ON p.id=vd.producto_id WHERE vd.venta_id=? ORDER BY vd.id',
            [$id]
        );
        return $venta;
    }

    public function existe(int $id): bool
    {
        return $this->fila('SELECT id FROM ventas WHERE id=?', [$id]) !== null;
    }

    public function crear(array $usuario, string $cliente, string $canal, array $items): array
    {
        $this->db->beginTransaction();
        try {
            $this->ejecutar(
                'INSERT INTO ventas (usuario_id,cliente,canal,total,costo_total) VALUES (?,?,?,0,0)',
                [$usuario['id'], $cliente === '' ? null : $cliente, $canal]
            );
            $ventaId = $this->ultimoId();
            $total = 0.0;
            $costo = 0.0;
            $tocados = [];
            foreach ($items as $it) {
                $p = $this->fila('SELECT id,precio_venta,precio_costo,stock FROM productos WHERE id=? AND activo=1 FOR UPDATE', [$it['producto_id']]);
                if (!$p) {
                    throw new ApiException('producto', 404);
                }
                if ((int)$p['stock'] < $it['cantidad']) {
                    throw new ApiException('stock_insuficiente', 400);
                }
                $total += (float)$p['precio_venta'] * $it['cantidad'];
                $costo += (float)$p['precio_costo'] * $it['cantidad'];
                $this->ejecutar(
                    'INSERT INTO venta_detalle (venta_id,producto_id,cantidad,precio_unitario,costo_unitario) VALUES (?,?,?,?,?)',
                    [$ventaId, $p['id'], $it['cantidad'], $p['precio_venta'], $p['precio_costo']]
                );
                $nuevo = (int)$p['stock'] - $it['cantidad'];
                $this->ejecutar('UPDATE productos SET stock=? WHERE id=?', [$nuevo, $p['id']]);
                $this->ejecutar(
                    'INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo) VALUES (?,?,?,?,?,?)',
                    [$p['id'], $usuario['id'], 'egreso', $it['cantidad'], $nuevo, 'Venta #' . $ventaId]
                );
                $tocados[] = (int)$p['id'];
            }
            $this->ejecutar('UPDATE ventas SET total=?,costo_total=? WHERE id=?', [$total, $costo, $ventaId]);
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
        $notificaciones = new NotificacionDAO();
        $notificaciones->crear('venta', 'Nueva venta #' . $ventaId . ' por $' . number_format($total, 2));
        foreach (array_unique($tocados) as $productoId) {
            $notificaciones->revisarStock($productoId);
        }
        return ['id' => $ventaId, 'total' => $total];
    }

    public function cambiarEstado(int $id, string $estado): void
    {
        $this->ejecutar('UPDATE ventas SET estado=? WHERE id=?', [$estado, $id]);
    }
}
