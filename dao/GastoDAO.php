<?php
class GastoDAO extends BaseDAO
{
    public static function listar(): array
    {
        $sql = 'SELECT g.*, c.nombre AS categoria FROM gastos g
                LEFT JOIN categorias_gasto c ON c.id=g.categoria_id
                ORDER BY g.fecha DESC';
        return self::pdo()->query($sql)->fetchAll();
    }
}
