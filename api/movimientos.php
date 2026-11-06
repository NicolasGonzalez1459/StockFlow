<?php
require __DIR__ . '/../php/autoload.php';

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        Sesion::requiere(['admin', 'vendedor', 'repositor']);
        $d = Validador::validar(['limite' => $p->consulta('limite')], [
            'limite' => ['tipo' => 'entero', 'min' => 1, 'max' => 200, 'defecto' => 50],
        ]);
        Respuesta::exito((new MovimientoDAO())->listar($d['limite']));
    },
    'POST' => function (Peticion $p): void {
        $usuario = Sesion::requiere(['admin', 'repositor']);
        $d = Validador::validar($p->cuerpo(), [
            'producto_id' => ['tipo' => 'entero', 'requerido' => true, 'min' => 1],
            'tipo' => ['tipo' => 'enum', 'requerido' => true, 'valores' => ['ingreso', 'egreso', 'ajuste']],
            'cantidad' => ['tipo' => 'entero', 'requerido' => true, 'min' => 0, 'max' => 1000000],
            'motivo' => ['tipo' => 'texto', 'min' => 0, 'max' => 200, 'defecto' => ''],
        ]);
        if ($d['tipo'] !== 'ajuste' && $d['cantidad'] < 1) {
            throw new ApiException('datos', 400, 'cantidad');
        }
        $resultado = (new MovimientoDAO())->registrar($d['producto_id'], $usuario['id'], $d['tipo'], $d['cantidad'], (string)$d['motivo']);
        Respuesta::exito($resultado, 'creado', 201);
    },
]);
