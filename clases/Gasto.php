<?php
class Gasto
{
    public static function listar(): array
    {
        return GastoDAO::listar();
    }

    public static function crear(?int $categoriaId, int $usuarioId, string $descripcion, float $monto, string $fecha): int
    {
        $pdo = Conexion::obtener();
        $st = $pdo->prepare('INSERT INTO gastos (categoria_id,usuario_id,descripcion,monto,fecha) VALUES (?,?,?,?,?)');
        $st->execute([$categoriaId ?: null, $usuarioId, $descripcion, $monto, $fecha]);
        return (int) $pdo->lastInsertId();
    }

    public static function eliminar(int $id): void
    {
        Conexion::obtener()->prepare('DELETE FROM gastos WHERE id=?')->execute([$id]);
    }
}
