<?php
require __DIR__ . '/../php/autoload.php';

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        Sesion::requiere(['admin', 'vendedor', 'repositor']);
        $d = Validador::validar(['limite' => $p->consulta('limite')], [
            'limite' => ['tipo' => 'entero', 'min' => 1, 'max' => 100, 'defecto' => 30],
        ]);
        Respuesta::exito((new NotificacionDAO())->listar($d['limite']));
    },
    'PUT' => function (Peticion $p): void {
        Sesion::requiere(['admin', 'vendedor', 'repositor']);
        $id = $p->idObligatorio();
        $dao = new NotificacionDAO();
        if (!$dao->obtener($id)) {
            throw new ApiException('no_encontrado', 404);
        }
        $dao->marcarLeida($id);
        Respuesta::exito($dao->obtener($id), 'actualizado');
    },
]);
