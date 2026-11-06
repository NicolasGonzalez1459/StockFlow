<?php
require __DIR__ . '/../php/autoload.php';

$reglas = [
    'codigo' => ['tipo' => 'texto', 'requerido' => true, 'max' => 40],
    'nombre' => ['tipo' => 'texto', 'requerido' => true, 'max' => 120],
    'categoria_id' => ['tipo' => 'entero', 'min' => 1],
    'unidad' => ['tipo' => 'texto', 'max' => 20, 'defecto' => 'unidad'],
    'precio_costo' => ['tipo' => 'decimal', 'requerido' => true, 'min' => 0],
    'precio_venta' => ['tipo' => 'decimal', 'requerido' => true, 'min' => 0],
    'stock' => ['tipo' => 'entero', 'min' => 0, 'max' => 1000000, 'defecto' => 0],
    'stock_minimo' => ['tipo' => 'entero', 'requerido' => true, 'min' => 0, 'max' => 1000000],
];

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        $usuario = Sesion::requiere(['admin', 'vendedor', 'repositor', 'cliente']);
        $catalogo = $usuario['rol'] === 'cliente';
        $dao = new ProductoDAO();
        $id = $p->id();
        if ($id !== null) {
            $producto = $dao->obtener($id, $catalogo);
            if (!$producto) {
                throw new ApiException('no_encontrado', 404);
            }
            Respuesta::exito($producto);
        }
        if ($p->consulta('stock_bajo') === '1') {
            Sesion::requiere(['admin', 'vendedor', 'repositor']);
            Respuesta::exito($dao->alertas());
        }
        Respuesta::exito($dao->listar($catalogo));
    },
    'POST' => function (Peticion $p) use ($reglas): void {
        $usuario = Sesion::requiere(['admin']);
        $d = Validador::validar($p->cuerpo(), $reglas);
        $id = (new ProductoDAO())->crear($d, $usuario['id']);
        Respuesta::exito(['id' => $id], 'creado', 201);
    },
    'PUT' => function (Peticion $p) use ($reglas): void {
        Sesion::requiere(['admin']);
        $id = $p->idObligatorio();
        $dao = new ProductoDAO();
        if (!$dao->existe($id)) {
            throw new ApiException('no_encontrado', 404);
        }
        unset($reglas['stock']);
        $d = Validador::validar($p->cuerpo(), $reglas);
        $dao->editar($id, $d);
        Respuesta::exito($dao->obtener($id, false), 'actualizado');
    },
    'DELETE' => function (Peticion $p): void {
        Sesion::requiere(['admin']);
        $id = $p->idObligatorio();
        $dao = new ProductoDAO();
        if (!$dao->existe($id)) {
            throw new ApiException('no_encontrado', 404);
        }
        $dao->desactivar($id);
        Respuesta::exito(['id' => $id], 'eliminado');
    },
]);
