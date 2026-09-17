<?php
class Producto
{
    public ?int $id = null;
    public string $codigo = '';
    public string $nombre = '';
    public ?int $categoria_id = null;
    public string $unidad = 'unidad';
    public float $precio_costo = 0;
    public float $precio_venta = 0;
    public int $stock = 0;
    public int $stock_minimo = 0;

    public static function listarActivos(bool $soloVenta = false): array
    {
        return ProductoDAO::listarActivos($soloVenta);
    }

    public static function buscarPorId(int $id): ?array
    {
        return ProductoDAO::buscarPorId($id);
    }

    public function crear(int $usuarioId): array
    {
        $pdo = Conexion::obtener();
        try {
            $st = $pdo->prepare('INSERT INTO productos (codigo,nombre,categoria_id,unidad,precio_costo,precio_venta,stock,stock_minimo) VALUES (?,?,?,?,?,?,?,?)');
            $st->execute([$this->codigo, $this->nombre, $this->categoria_id ?: null, $this->unidad, $this->precio_costo, $this->precio_venta, $this->stock, $this->stock_minimo]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') return ['ok' => false, 'error' => 'codigo_duplicado'];
            return ['ok' => false, 'error' => 'db'];
        }
        $id = (int) $pdo->lastInsertId();
        if ($this->stock > 0) {
            MovimientoStock::registrar($id, $usuarioId, 'ingreso', $this->stock, $this->stock, 'Alta de producto');
        }
        self::revisarStock($id);
        return ['ok' => true, 'id' => $id];
    }

    public function actualizar(): array
    {
        try {
            $st = Conexion::obtener()->prepare('UPDATE productos SET codigo=?,nombre=?,categoria_id=?,unidad=?,precio_costo=?,precio_venta=?,stock_minimo=? WHERE id=?');
            $st->execute([$this->codigo, $this->nombre, $this->categoria_id ?: null, $this->unidad, $this->precio_costo, $this->precio_venta, $this->stock_minimo, $this->id]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') return ['ok' => false, 'error' => 'codigo_duplicado'];
            return ['ok' => false, 'error' => 'db'];
        }
        self::revisarStock($this->id);
        return ['ok' => true];
    }

    public static function eliminar(int $id): void
    {
        $st = Conexion::obtener()->prepare('UPDATE productos SET activo=0 WHERE id=?');
        $st->execute([$id]);
    }

    public static function registrarMovimiento(int $productoId, string $tipo, int $cantidad, int $usuarioId, string $motivo = ''): array
    {
        $pdo = Conexion::obtener();
        $st = $pdo->prepare('SELECT stock FROM productos WHERE id=?');
        $st->execute([$productoId]);
        $p = $st->fetch();
        if (!$p) return ['ok' => false, 'error' => 'producto'];
        $stockActual = (int) $p['stock'];
        if ($tipo === 'ingreso') $nuevo = $stockActual + $cantidad;
        elseif ($tipo === 'egreso') $nuevo = $stockActual - $cantidad;
        else $nuevo = $cantidad;
        if ($nuevo < 0) return ['ok' => false, 'error' => 'stock_insuficiente'];
        $pdo->prepare('UPDATE productos SET stock=? WHERE id=?')->execute([$nuevo, $productoId]);
        MovimientoStock::registrar($productoId, $usuarioId, $tipo, $cantidad, $nuevo, $motivo);
        self::revisarStock($productoId);
        return ['ok' => true, 'stock' => $nuevo];
    }

    public static function guardarImagen(int $id, array $archivo): array
    {
        $st = Conexion::obtener()->prepare('SELECT id FROM productos WHERE id=?');
        $st->execute([$id]);
        if (!$st->fetch()) return ['ok' => false, 'error' => 'producto'];
        if (empty($archivo['name']) || $archivo['error'] !== UPLOAD_ERR_OK) return ['ok' => false, 'error' => 'archivo'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) return ['ok' => false, 'error' => 'formato'];
        if ($archivo['size'] > 3 * 1024 * 1024) return ['ok' => false, 'error' => 'tamano'];
        $dir = __DIR__ . '/../img/productos';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $nombre = 'p' . $id . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($archivo['tmp_name'], $dir . '/' . $nombre)) return ['ok' => false, 'error' => 'subida'];
        $ruta = 'img/productos/' . $nombre;
        Conexion::obtener()->prepare('UPDATE productos SET imagen=? WHERE id=?')->execute([$ruta, $id]);
        return ['ok' => true, 'imagen' => $ruta];
    }

    public static function alertasStock(): array
    {
        return Conexion::obtener()->query('SELECT id,codigo,nombre,stock,stock_minimo FROM productos WHERE activo=1 AND stock<=stock_minimo ORDER BY stock')->fetchAll();
    }

    public static function revisarStock(int $id): void
    {
        $st = Conexion::obtener()->prepare('SELECT nombre,stock,stock_minimo FROM productos WHERE id=?');
        $st->execute([$id]);
        $p = $st->fetch();
        if ($p && $p['stock'] <= $p['stock_minimo']) {
            Notificacion::crear('stock_minimo', 'Stock minimo alcanzado: ' . $p['nombre'] . ' (' . $p['stock'] . ')');
        }
    }
}
