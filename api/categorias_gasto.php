<?php
require __DIR__ . '/../php/autoload.php';

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        Sesion::requiere(['admin']);
        Respuesta::exito((new CategoriaGastoDAO())->listar());
    },
]);
