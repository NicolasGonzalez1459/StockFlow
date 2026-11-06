<?php

final class MovimientoDAO extends DAO
{
    public function listar(int $limite): array
    {
        return $this->consultar(
            'SELECT m.id,m.producto_id,m.tipo,m.cantidad,m.stock_resultante,m.motivo,m.fecha,p.nombre AS producto,u.nombre AS usuario
             FROM movimientos_stock m
             LEFT JOIN productos p ON p.id=m.producto_id
             LEFT JOIN usuarios u ON u.id=m.usuario_id
             ORDER BY m.fecha DESC, m.id DESC LIMIT ' . $limite
        );
    }

    public function registrar(int $productoId, int $usuarioId, string $tipo, int $cantidad, string $motivo): array
    {
        $this->db->beginTransaction();
        try {
            $p = $this->fila('SELECT stock FROM productos WHERE id=? AND activo=1 FOR UPDATE', [$productoId]);
            if (!$p) {
                throw new ApiException('producto', 404);
            }
            $actual = (int)$p['stock'];
            $nuevo = $tipo === 'ingreso' ? $actual + $cantidad : ($tipo === 'egreso' ? $actual - $cantidad : $cantidad);
            if ($nuevo < 0) {
                throw new ApiException('stock_insuficiente', 400);
            }
            $this->ejecutar('UPDATE productos SET stock=? WHERE id=?', [$nuevo, $productoId]);
            $this->ejecutar(
                'INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo) VALUES (?,?,?,?,?,?)',
                [$productoId, $usuarioId, $tipo, $cantidad, $nuevo, $motivo]
            );
            $id = $this->ultimoId();
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
        (new NotificacionDAO())->revisarStock($productoId);
        return ['id' => $id, 'producto_id' => $productoId, 'stock' => $nuevo];
    }
}
