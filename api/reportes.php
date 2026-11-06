<?php
require __DIR__ . '/../php/autoload.php';

Api::ejecutar([
    'GET' => function (Peticion $p): void {
        Sesion::requiere(['admin']);
        $d = Validador::validar([
            'tipo' => $p->consulta('tipo'),
            'desde' => $p->consulta('desde'),
            'hasta' => $p->consulta('hasta'),
        ], [
            'tipo' => ['tipo' => 'enum', 'valores' => ['dashboard', 'ventas', 'stock', 'gastos', 'rentabilidad'], 'defecto' => 'dashboard'],
            'desde' => ['tipo' => 'fecha', 'defecto' => date('Y-m-01')],
            'hasta' => ['tipo' => 'fecha', 'defecto' => date('Y-m-d')],
        ]);
        if ($d['desde'] > $d['hasta']) {
            throw new ApiException('datos', 400, 'desde');
        }
        $dao = new ReporteDAO();
        switch ($d['tipo']) {
            case 'ventas':
                Respuesta::exito($dao->ventas($d['desde'], $d['hasta']));
            case 'stock':
                Respuesta::exito($dao->stock());
            case 'gastos':
                Respuesta::exito($dao->gastos($d['desde'], $d['hasta']));
            case 'rentabilidad':
                Respuesta::exito($dao->rentabilidad($d['desde'], $d['hasta']));
            default:
                Respuesta::exito($dao->dashboard($d['desde'], $d['hasta']));
        }
    },
]);
