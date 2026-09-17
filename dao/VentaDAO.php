<?php
class VentaDAO extends BaseDAO
{
    public static function listar(array $usuario): array
    {
        $sql = 'SELECT v.*, u.nombre AS vendedor FROM ventas v LEFT JOIN usuarios u ON u.id=v.usuario_id';
        $par = [];

        if ($usuario['rol'] === 'vendedor') {
            $sql .= ' WHERE v.usuario_id=?';
            $par[] = $usuario['id'];
        } elseif ($usuario['rol'] === 'cliente') {
            $sql .= ' WHERE v.cliente=?';
            $par[] = $usuario['nombre'];
        }

        $sql .= ' ORDER BY v.fecha DESC';
        $st = self::pdo()->prepare($sql);
        $st->execute($par);
        return $st->fetchAll();
    }

    public static function detalle(int $ventaId): array
    {
        $st = self::pdo()->prepare('SELECT vd.*, p.nombre AS producto FROM venta_detalle vd LEFT JOIN productos p ON p.id=vd.producto_id WHERE vd.venta_id=?');
        $st->execute([$ventaId]);
        return $st->fetchAll();
    }
}
