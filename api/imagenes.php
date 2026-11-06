<?php
require __DIR__ . '/../php/autoload.php';

Api::ejecutar([
    'POST' => function (Peticion $p): void {
        Sesion::requiere(['admin']);
        $d = Validador::validar($p->formulario(), [
            'producto_id' => ['tipo' => 'entero', 'requerido' => true, 'min' => 1],
        ]);
        $dao = new ProductoDAO();
        if (!$dao->existe($d['producto_id'])) {
            throw new ApiException('no_encontrado', 404);
        }
        $ruta = Imagen::guardar($p->archivo('imagen'), $d['producto_id']);
        $dao->actualizarImagen($d['producto_id'], $ruta);
        Respuesta::exito(['producto_id' => $d['producto_id'], 'imagen' => $ruta], 'imagen_subida', 201);
    },
]);
