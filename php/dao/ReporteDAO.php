<?php

final class ReporteDAO extends DAO
{
    public function dashboard(string $desde, string $hasta): array
    {
        $v = $this->fila(
            'SELECT COUNT(*) c, COALESCE(SUM(total),0) t, COALESCE(SUM(costo_total),0) k FROM ventas WHERE DATE(fecha) BETWEEN ? AND ?',
            [$desde, $hasta]
        );
        $g = $this->fila('SELECT COALESCE(SUM(monto),0) g FROM gastos WHERE fecha BETWEEN ? AND ?', [$desde, $hasta]);
        $top = $this->consultar(
            'SELECT p.nombre, SUM(vd.cantidad) cant, SUM(vd.cantidad*vd.precio_unitario) total FROM venta_detalle vd
             JOIN ventas v ON v.id=vd.venta_id LEFT JOIN productos p ON p.id=vd.producto_id
             WHERE DATE(v.fecha) BETWEEN ? AND ? GROUP BY vd.producto_id, p.nombre ORDER BY cant DESC LIMIT 5',
            [$desde, $hasta]
        );
        $serie = $this->consultar(
            'SELECT DATE(fecha) dia, SUM(total) total FROM ventas WHERE DATE(fecha) BETWEEN ? AND ? GROUP BY DATE(fecha) ORDER BY dia',
            [$desde, $hasta]
        );
        $criticos = $this->fila('SELECT COUNT(*) c FROM productos WHERE activo=1 AND stock<=stock_minimo');
        $ventasTotal = (float)$v['t'];
        $costoTotal = (float)$v['k'];
        $gastosTotal = (float)$g['g'];
        return [
            'ventas_cantidad' => (int)$v['c'],
            'ventas_total' => $ventasTotal,
            'costo_total' => $costoTotal,
            'gastos_total' => $gastosTotal,
            'margen' => $ventasTotal - $costoTotal - $gastosTotal,
            'stock_critico' => (int)$criticos['c'],
            'top' => $top,
            'serie' => $serie,
        ];
    }

    public function ventas(string $desde, string $hasta): array
    {
        return $this->consultar(
            'SELECT v.id, v.fecha, v.cliente, v.canal, u.nombre vendedor, v.total, v.estado FROM ventas v
             LEFT JOIN usuarios u ON u.id=v.usuario_id WHERE DATE(v.fecha) BETWEEN ? AND ? ORDER BY v.fecha',
            [$desde, $hasta]
        );
    }

    public function stock(): array
    {
        return $this->consultar(
            'SELECT codigo,nombre,stock,stock_minimo,precio_costo,precio_venta,(stock*precio_costo) valorizado FROM productos WHERE activo=1 ORDER BY nombre'
        );
    }

    public function gastos(string $desde, string $hasta): array
    {
        return $this->consultar(
            'SELECT g.fecha, c.nombre categoria, g.descripcion, g.monto FROM gastos g
             LEFT JOIN categorias_gasto c ON c.id=g.categoria_id WHERE g.fecha BETWEEN ? AND ? ORDER BY g.fecha',
            [$desde, $hasta]
        );
    }

    public function rentabilidad(string $desde, string $hasta): array
    {
        return $this->consultar(
            'SELECT p.nombre, SUM(vd.cantidad) unidades, SUM(vd.cantidad*vd.precio_unitario) ingresos, SUM(vd.cantidad*vd.costo_unitario) costos,
                    SUM(vd.cantidad*(vd.precio_unitario - vd.costo_unitario)) margen
             FROM venta_detalle vd JOIN ventas v ON v.id=vd.venta_id LEFT JOIN productos p ON p.id=vd.producto_id
             WHERE DATE(v.fecha) BETWEEN ? AND ? GROUP BY vd.producto_id, p.nombre ORDER BY margen DESC',
            [$desde, $hasta]
        );
    }
}
