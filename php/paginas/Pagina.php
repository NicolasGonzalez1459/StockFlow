<?php
declare(strict_types=1);

abstract class Pagina
{
    protected string $slug = '';
    protected string $tituloClave = '';
    protected string $css = '';
    protected array $roles = [];
    protected array $scripts = [];
    protected ?array $usuario = null;

    abstract protected function cuerpo(): string;

    public function mostrar(): void
    {
        $this->usuario = Sesion::usuario();
        if (!$this->usuario) {
            Respuesta::redirigir('index.php?p=login');
        }
        if ($this->roles && !in_array($this->usuario['rol'], $this->roles, true)) {
            Respuesta::redirigir('index.php?p=' . Menu::inicio($this->usuario['rol']));
        }
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store');
        echo $this->maqueta($this->cuerpo());
    }

    protected function e($valor): string
    {
        return Formato::e($valor);
    }

    protected function t(string $clave): string
    {
        return Idioma::t($clave);
    }

    protected function selectorIdioma(): string
    {
        $actual = Idioma::actual();
        return '<select id="lang" style="width:auto" data-idioma>'
            . '<option value="es"' . ($actual === 'es' ? ' selected' : '') . '>ES</option>'
            . '<option value="en"' . ($actual === 'en' ? ' selected' : '') . '>EN</option></select>';
    }

    protected function cabeza(string $titulo, string $css): string
    {
        $v = $this->e(Config::get('version_assets'));
        $comun = $css === 'login' ? '' : '<link rel="stylesheet" href="css/layout.css?v=' . $v . '">';
        return '<!DOCTYPE html><html lang="' . $this->e(Idioma::actual()) . '"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . $this->e($titulo) . ' | StockFlow</title>'
            . '<link rel="shortcut icon" href="img/icono.ico" type="image/x-icon">'
            . '<link rel="stylesheet" href="css/' . $this->e($css) . '.css?v=' . $v . '">' . $comun
            . '<link rel="stylesheet" href="css/aviso.css?v=' . $v . '"></head><body>';
    }

    protected function pie(array $scripts): string
    {
        $v = $this->e(Config::get('version_assets'));
        $flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP;
        $contexto = [
            'rol' => $this->usuario['rol'] ?? null,
            'usuario' => $this->usuario ? ['id' => $this->usuario['id'], 'nombre' => $this->usuario['nombre']] : null,
            'empresa' => (string)Config::get('empresa'),
            'idioma' => Idioma::actual(),
        ];
        $html = '<div id="avisos" aria-live="polite"></div>'
            . '<script type="application/json" id="textos">' . json_encode(Idioma::todo(), $flags) . '</script>'
            . '<script type="application/json" id="contexto">' . json_encode($contexto, $flags) . '</script>';
        foreach (array_merge(['api', 'ui'], $scripts) as $js) {
            $html .= '<script src="js/' . $this->e($js) . '.js?v=' . $v . '"></script>';
        }
        return $html . '</body></html>';
    }

    protected function maqueta(string $contenido): string
    {
        $empresa = $this->e(Config::get('empresa'));
        $menu = '';
        foreach (Menu::paraRol($this->usuario['rol']) as $item) {
            $menu .= '<a href="index.php?p=' . $this->e($item['id']) . '" class="' . ($item['id'] === $this->slug ? 'on' : '') . '">'
                . $this->e($this->t($item['clave'])) . '</a>';
        }
        $html = $this->cabeza($this->t($this->tituloClave), $this->css);
        $html .= '<div class="layout" id="layout"><aside class="side" id="side"><div class="side-head"><div class="brand"><div class="logo" style="width:40px;height:40px;flex:0 0 40px;border-radius:50%;background:transparent;overflow:hidden"><img src="img/logo_menu.png" alt="Logo" width="40" height="40" style="display:block;width:100%;height:100%;object-fit:cover"></div><span class="brand-name">Stock<strong>Flow</strong></span></div><button type="button" class="menu-btn" data-menu aria-label="Abrir o cerrar menu" aria-expanded="false"><span></span><span></span><span></span></button></div>'
            . '<nav id="nav">' . $menu . '</nav></aside><button type="button" class="menu-cover" data-menu aria-label="Cerrar menu"></button><main class="main"><div class="top">'
            . '<div class="title-row"><button type="button" class="menu-btn menu-mobile" data-menu aria-label="Abrir menu" aria-expanded="false"><span></span><span></span><span></span></button><div><h1>' . $this->e($this->t($this->tituloClave)) . '</h1><div class="h-sub">' . $empresa . ' · BT Informatica 2026</div></div></div>'
            . '<div class="top-right"><span class="chip" id="who">' . $this->e($this->usuario['nombre'] . ' · ' . $this->usuario['rol']) . '</span>'
            . $this->selectorIdioma()
            . '<button type="button" class="btn sec" id="salir" data-salir>' . $this->e($this->t('logout')) . '</button>'
            . '</div></div>'
            . $contenido
            . '<p class="foot">POLO EDUCATIVO TECNOLOGICO — CETP-UTU | Proyecto de Egreso BT Informatica 2026</p></main></div>';
        return $html . $this->pie($this->scripts);
    }
}
