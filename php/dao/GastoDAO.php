<?php

final class GastoDAO extends DAO
{
    public function listar(): array
    {
        return $this->consultar(
            'SELECT g.id,g.categoria_id,g.descripcion,g.monto,g.fecha,c.nombre AS categoria
             FROM gastos g LEFT JOIN categorias_gasto c ON c.id=g.categoria_id
             ORDER BY g.fecha DESC, g.id DESC'
        );
    }

    public function obtener(int $id): ?array
    {
        return $this->fila(
            'SELECT g.id,g.categoria_id,g.descripcion,g.monto,g.fecha,c.nombre AS categoria
             FROM gastos g LEFT JOIN categorias_gasto c ON c.id=g.categoria_id WHERE g.id=?',
            [$id]
        );
    }

    public function crear(array $d, int $usuarioId): int
    {
        $this->ejecutar(
            'INSERT INTO gastos (categoria_id,usuario_id,descripcion,monto,fecha) VALUES (?,?,?,?,?)',
            [$d['categoria_id'], $usuarioId, $d['descripcion'], $d['monto'], $d['fecha']]
        );
        return $this->ultimoId();
    }

    public function editar(int $id, array $d): void
    {
        $this->ejecutar(
            'UPDATE gastos SET categoria_id=?,descripcion=?,monto=?,fecha=? WHERE id=?',
            [$d['categoria_id'], $d['descripcion'], $d['monto'], $d['fecha'], $id]
        );
    }

    public function eliminar(int $id): void
    {
        $this->ejecutar('DELETE FROM gastos WHERE id=?', [$id]);
    }
}
