<?php
class ProductoDAO extends BaseDAO
{
    public static function listarActivos(bool $soloVenta = false): array
    {
        if ($soloVenta) {
            $sql = 'SELECT p.id,p.codigo,p.nombre,p.categoria_id,p.unidad,p.precio_venta,p.stock,p.imagen,c.nombre AS categoria
                    FROM productos p LEFT JOIN categorias_producto c ON c.id=p.categoria_id
                    WHERE p.activo=1 ORDER BY p.nombre';
        } else {
            $sql = 'SELECT p.*, c.nombre AS categoria FROM productos p
                    LEFT JOIN categorias_producto c ON c.id=p.categoria_id
                    WHERE p.activo=1 ORDER BY p.nombre';
        }

        return self::pdo()->query($sql)->fetchAll();
    }

    public static function buscarPorId(int $id): ?array
    {
        $st = self::pdo()->prepare('SELECT * FROM productos WHERE id=?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public static function alertasStock(): array
    {
        return self::pdo()->query('SELECT id,codigo,nombre,stock,stock_minimo FROM productos WHERE activo=1 AND stock<=stock_minimo ORDER BY stock')->fetchAll();
    }
}
