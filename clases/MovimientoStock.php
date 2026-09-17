<?php
class MovimientoStock
{
    public static function registrar(int $productoId, ?int $usuarioId, string $tipo, int $cantidad, int $stockResultante, string $motivo = ''): void
    {
        $st = Conexion::obtener()->prepare('INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo) VALUES (?,?,?,?,?,?)');
        $st->execute([$productoId, $usuarioId, $tipo, $cantidad, $stockResultante, $motivo]);
    }

    public static function listarUltimos(int $limite = 200): array
    {
        $sql = 'SELECT m.*, p.nombre AS producto, u.nombre AS usuario FROM movimientos_stock m
                LEFT JOIN productos p ON p.id=m.producto_id
                LEFT JOIN usuarios u ON u.id=m.usuario_id
                ORDER BY m.fecha DESC LIMIT ' . (int) $limite;
        return Conexion::obtener()->query($sql)->fetchAll();
    }
}
