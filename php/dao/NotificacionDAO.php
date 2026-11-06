<?php

final class NotificacionDAO extends DAO
{
    public function crear(string $tipo, string $mensaje): void
    {
        $this->ejecutar('INSERT INTO notificaciones (tipo,mensaje) VALUES (?,?)', [$tipo, $mensaje]);
    }

    public function listar(int $limite): array
    {
        return $this->consultar('SELECT id,tipo,mensaje,leida,fecha FROM notificaciones ORDER BY fecha DESC, id DESC LIMIT ' . $limite);
    }

    public function obtener(int $id): ?array
    {
        return $this->fila('SELECT id,tipo,mensaje,leida,fecha FROM notificaciones WHERE id=?', [$id]);
    }

    public function marcarLeida(int $id): void
    {
        $this->ejecutar('UPDATE notificaciones SET leida=1 WHERE id=?', [$id]);
    }

    public function revisarStock(int $productoId): void
    {
        $p = $this->fila('SELECT nombre,stock,stock_minimo FROM productos WHERE id=?', [$productoId]);
        if ($p && (int)$p['stock'] <= (int)$p['stock_minimo']) {
            $this->crear('stock_minimo', 'Stock minimo alcanzado: ' . $p['nombre'] . ' (' . $p['stock'] . ')');
        }
    }
}
