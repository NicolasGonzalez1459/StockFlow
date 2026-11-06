<?php

final class Aplicacion
{
    private const PAGINAS = [
        'login' => 'LoginPagina',
        'dashboard' => 'DashboardPagina',
        'inventario' => 'InventarioPagina',
        'ventas' => 'VentasPagina',
        'gastos' => 'GastosPagina',
        'reportes' => 'ReportesPagina',
        'usuarios' => 'UsuariosPagina',
        'portal' => 'PortalPagina',
    ];

    private string $raiz;

    public function __construct(string $raiz)
    {
        $this->raiz = $raiz;
    }

    public function ejecutar(): void
    {
        Config::cargar($this->raiz);
        ini_set('display_errors', Config::get('debug') ? '1' : '0');
        date_default_timezone_set((string)Config::get('zona_horaria', 'UTC'));
        Sesion::iniciar();

        if (isset($_GET['lang'])) {
            Idioma::establecer((string)$_GET['lang']);
            $destino = strtok($_SERVER['REQUEST_URI'] ?? 'index.php', '?');
            $resto = $_GET;
            unset($resto['lang']);
            Respuesta::redirigir($destino . ($resto ? '?' . http_build_query($resto) : ''));
        }

        $slug = (string)($_GET['p'] ?? '');

        if ($slug === '') {
            $usuario = Sesion::usuario();
            Respuesta::redirigir('index.php?p=' . ($usuario ? Menu::inicio($usuario['rol']) : 'login'));
        }

        if (!isset(self::PAGINAS[$slug])) {
            http_response_code(404);
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><title>404</title></head><body><p>'
                . Formato::e(Idioma::t('not_found')) . '</p><p><a href="index.php">StockFlow</a></p></body></html>';
            return;
        }

        $clase = self::PAGINAS[$slug];
        (new $clase())->mostrar();
    }
}
