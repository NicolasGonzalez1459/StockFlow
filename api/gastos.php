<?php
require __DIR__ . '/../php/autoload.php';

$reglas = [
    'categoria_id' => ['tipo' => 'entero', 'min' => 1],
    'descripcion' => ['tipo' => 'texto', 'requerido' => true, 'max' => 200],
    'monto' => ['tipo' => 'decimal', 'requerido' => true, 'min' => 0.01, 'max' => 100000000],
    'fecha' => ['tipo' => 'fecha', 'requerido' => true],
];

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        Sesion::requiere(['admin']);
        $dao = new GastoDAO();
        $id = $p->id();
        if ($id !== null) {
            $gasto = $dao->obtener($id);
            if (!$gasto) {
                throw new ApiException('no_encontrado', 404);
            }
            Respuesta::exito($gasto);
        }
        Respuesta::exito($dao->listar());
    },
    'POST' => function (Peticion $p) use ($reglas): void {
        $usuario = Sesion::requiere(['admin']);
        $d = Validador::validar($p->cuerpo(), $reglas);
        $id = (new GastoDAO())->crear($d, $usuario['id']);
        Respuesta::exito(['id' => $id], 'creado', 201);
    },
    'PUT' => function (Peticion $p) use ($reglas): void {
        Sesion::requiere(['admin']);
        $id = $p->idObligatorio();
        $dao = new GastoDAO();
        if (!$dao->obtener($id)) {
            throw new ApiException('no_encontrado', 404);
        }
        $d = Validador::validar($p->cuerpo(), $reglas);
        $dao->editar($id, $d);
        Respuesta::exito($dao->obtener($id), 'actualizado');
    },
    'DELETE' => function (Peticion $p): void {
        Sesion::requiere(['admin']);
        $id = $p->idObligatorio();
        $dao = new GastoDAO();
        if (!$dao->obtener($id)) {
            throw new ApiException('no_encontrado', 404);
        }
        $dao->eliminar($id);
        Respuesta::exito(['id' => $id], 'eliminado');
    },
]);
