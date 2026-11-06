<?php

final class ProductoDAO extends DAO
{
    private const COLUMNAS_CATALOGO = 'p.id,p.codigo,p.nombre,p.categoria_id,p.unidad,p.precio_venta,p.stock,p.imagen,c.nombre AS categoria';
    private const COLUMNAS_COMPLETAS = 'p.id,p.codigo,p.nombre,p.categoria_id,p.unidad,p.precio_costo,p.precio_venta,p.stock,p.stock_minimo,p.imagen,c.nombre AS categoria';

    public function listar(bool $soloCatalogo): array
    {
        $columnas = $soloCatalogo ? self::COLUMNAS_CATALOGO : self::COLUMNAS_COMPLETAS;
        return $this->consultar(
            'SELECT ' . $columnas . ' FROM productos p LEFT JOIN categorias_producto c ON c.id=p.categoria_id WHERE p.activo=1 ORDER BY p.nombre'
        );
    }

    public function obtener(int $id, bool $soloCatalogo): ?array
    {
        $columnas = $soloCatalogo ? self::COLUMNAS_CATALOGO : self::COLUMNAS_COMPLETAS;
        return $this->fila(
            'SELECT ' . $columnas . ' FROM productos p LEFT JOIN categorias_producto c ON c.id=p.categoria_id WHERE p.activo=1 AND p.id=?',
            [$id]
        );
    }

    public function alertas(): array
    {
        return $this->consultar(
            'SELECT id,codigo,nombre,stock,stock_minimo FROM productos WHERE activo=1 AND stock<=stock_minimo ORDER BY stock, nombre'
        );
    }

    public function crear(array $d, int $usuarioId): int
    {
        $this->db->beginTransaction();
        try {
            $this->ejecutar(
                'INSERT INTO productos (codigo,nombre,categoria_id,unidad,precio_costo,precio_venta,stock,stock_minimo) VALUES (?,?,?,?,?,?,?,?)',
                [$d['codigo'], $d['nombre'], $d['categoria_id'], $d['unidad'], $d['precio_costo'], $d['precio_venta'], $d['stock'], $d['stock_minimo']]
            );
            $id = $this->ultimoId();
            if ($d['stock'] > 0) {
                $this->ejecutar(
                    'INSERT INTO movimientos_stock (producto_id,usuario_id,tipo,cantidad,stock_resultante,motivo) VALUES (?,?,?,?,?,?)',
                    [$id, $usuarioId, 'ingreso', $d['stock'], $d['stock'], 'Alta de producto']
                );
            }
            $this->db->commit();
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            if ($e->getCode() === '23000') {
                throw new ApiException('codigo_duplicado', 400);
            }
            throw $e;
        }
        (new NotificacionDAO())->revisarStock($id);
        return $id;
    }

    public function editar(int $id, array $d): void
    {
        try {
            $this->ejecutar(
                'UPDATE productos SET codigo=?,nombre=?,categoria_id=?,unidad=?,precio_costo=?,precio_venta=?,stock_minimo=? WHERE id=?',
                [$d['codigo'], $d['nombre'], $d['categoria_id'], $d['unidad'], $d['precio_costo'], $d['precio_venta'], $d['stock_minimo'], $id]
            );
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new ApiException('codigo_duplicado', 400);
            }
            throw $e;
        }
        (new NotificacionDAO())->revisarStock($id);
    }

    public function desactivar(int $id): void
    {
        $this->ejecutar('UPDATE productos SET activo=0 WHERE id=?', [$id]);
    }

    public function actualizarImagen(int $id, string $ruta): void
    {
        $this->ejecutar('UPDATE productos SET imagen=? WHERE id=?', [$ruta, $id]);
    }

    public function existe(int $id): bool
    {
        return $this->fila('SELECT id FROM productos WHERE id=? AND activo=1', [$id]) !== null;
    }
}
