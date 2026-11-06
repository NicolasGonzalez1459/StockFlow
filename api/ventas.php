<?php
require __DIR__ . '/../php/autoload.php';

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        $usuario = Sesion::requiere(['admin', 'vendedor', 'cliente']);
        $dao = new VentaDAO();
        $id = $p->id();
        if ($id !== null) {
            $venta = $dao->obtener($id, $usuario);
            if (!$venta) {
                throw new ApiException('no_encontrado', 404);
            }
            Respuesta::exito($venta);
        }
        Respuesta::exito($dao->listar($usuario));
    },
    'POST' => function (Peticion $p): void {
        $usuario = Sesion::requiere(['admin', 'vendedor', 'cliente']);
        $cuerpo = $p->cuerpo();
        $items = Validador::lista($cuerpo['items'] ?? null, 'items', 50, [
            'producto_id' => ['tipo' => 'entero', 'requerido' => true, 'min' => 1],
            'cantidad' => ['tipo' => 'entero', 'requerido' => true, 'min' => 1, 'max' => 100000],
        ]);
        if ($usuario['rol'] === 'cliente') {
            $cliente = $usuario['nombre'];
            $canal = 'web';
        } else {
            $d = Validador::validar($cuerpo, [
                'cliente' => ['tipo' => 'texto', 'max' => 120, 'defecto' => ''],
                'canal' => ['tipo' => 'enum', 'valores' => ['tienda_online', 'marketplace', 'redes', 'presencial', 'web'], 'defecto' => 'web'],
            ]);
            $cliente = (string)$d['cliente'];
            $canal = $d['canal'];
        }
        $resultado = (new VentaDAO())->crear($usuario, $cliente, $canal, $items);
        Respuesta::exito($resultado, 'creado', 201);
    },
    'PUT' => function (Peticion $p): void {
        $usuario = Sesion::requiere(['admin', 'vendedor']);
        $id = $p->idObligatorio();
        $dao = new VentaDAO();
        if (!$dao->obtener($id, $usuario)) {
            throw new ApiException('no_encontrado', 404);
        }
        $d = Validador::validar($p->cuerpo(), [
            'estado' => ['tipo' => 'enum', 'requerido' => true, 'valores' => VentaDAO::ESTADOS],
        ]);
        $dao->cambiarEstado($id, $d['estado']);
        Respuesta::exito($dao->obtener($id, $usuario), 'status_actualizado');
    },
]);
