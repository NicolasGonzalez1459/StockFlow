<?php
require __DIR__ . '/../php/autoload.php';

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        Respuesta::exito(['usuario' => Sesion::requiere()]);
    },
    'POST' => function (Peticion $p): void {
        $d = Validador::validar($p->cuerpo(), [
            'email' => ['tipo' => 'email', 'requerido' => true],
            'password' => ['tipo' => 'texto', 'requerido' => true, 'max' => 200, 'recortar' => false],
        ]);
        $usuario = (new UsuarioDAO())->autenticar($d['email'], $d['password']);
        if (!$usuario) {
            throw new ApiException('credenciales', 401);
        }
        Sesion::entrar($usuario);
        Respuesta::exito(['usuario' => Sesion::usuario(), 'inicio' => Menu::inicio($usuario['rol'])], 'sesion_iniciada');
    },
    'DELETE' => function (Peticion $p): void {
        Sesion::salir();
        Respuesta::exito(null, 'sesion_cerrada');
    },
]);
