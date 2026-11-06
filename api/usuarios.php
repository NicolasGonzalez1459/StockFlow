<?php
require __DIR__ . '/../php/autoload.php';

$reglas = [
    'nombre' => ['tipo' => 'texto', 'requerido' => true, 'max' => 100],
    'email' => ['tipo' => 'email', 'requerido' => true],
    'rol' => ['tipo' => 'enum', 'requerido' => true, 'valores' => UsuarioDAO::ROLES],
    'activo' => ['tipo' => 'booleano', 'defecto' => 1],
];

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        Sesion::requiere(['admin']);
        $dao = new UsuarioDAO();
        $id = $p->id();
        if ($id !== null) {
            $usuario = $dao->obtener($id);
            if (!$usuario) {
                throw new ApiException('no_encontrado', 404);
            }
            Respuesta::exito($usuario);
        }
        Respuesta::exito($dao->listar());
    },
    'POST' => function (Peticion $p) use ($reglas): void {
        Sesion::requiere(['admin']);
        $reglas['password'] = ['tipo' => 'texto', 'requerido' => true, 'min' => 6, 'max' => 100, 'recortar' => false];
        $d = Validador::validar($p->cuerpo(), $reglas);
        $id = (new UsuarioDAO())->crear($d);
        Respuesta::exito(['id' => $id], 'creado', 201);
    },
    'PUT' => function (Peticion $p) use ($reglas): void {
        $actual = Sesion::requiere(['admin']);
        $id = $p->idObligatorio();
        $dao = new UsuarioDAO();
        if (!$dao->obtener($id)) {
            throw new ApiException('no_encontrado', 404);
        }
        $reglas['password'] = ['tipo' => 'texto', 'min' => 6, 'max' => 100, 'recortar' => false];
        $d = Validador::validar($p->cuerpo(), $reglas);
        if ($id === $actual['id'] && ($d['activo'] === 0 || $d['rol'] !== 'admin')) {
            throw new ApiException('no_autoeliminar', 400);
        }
        $dao->editar($id, $d);
        Respuesta::exito($dao->obtener($id), 'actualizado');
    },
    'DELETE' => function (Peticion $p): void {
        $actual = Sesion::requiere(['admin']);
        $id = $p->idObligatorio();
        $dao = new UsuarioDAO();
        if (!$dao->obtener($id)) {
            throw new ApiException('no_encontrado', 404);
        }
        if ($id === $actual['id']) {
            throw new ApiException('no_autoeliminar', 400);
        }
        $dao->desactivar($id);
        Respuesta::exito(['id' => $id], 'eliminado');
    },
]);
