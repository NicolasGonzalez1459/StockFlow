<?php
class Notificacion
{
    public static function crear(string $tipo, string $mensaje): void
    {
        $st = Conexion::obtener()->prepare('INSERT INTO notificaciones (tipo,mensaje) VALUES (?,?)');
        $st->execute([$tipo, $mensaje]);
    }

    public static function listar(int $limite = 30): array
    {
        $st = Conexion::obtener()->prepare('SELECT * FROM notificaciones ORDER BY fecha DESC LIMIT ' . (int) $limite);
        $st->execute();
        return $st->fetchAll();
    }

    public static function marcarLeida(int $id): void
    {
        $st = Conexion::obtener()->prepare('UPDATE notificaciones SET leida=1 WHERE id=?');
        $st->execute([$id]);
    }
}
