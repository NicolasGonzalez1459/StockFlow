<?php

final class LoginPagina extends Pagina
{
    protected string $slug = 'login';
    protected string $tituloClave = 'tagline';
    protected string $css = 'login';
    protected array $scripts = ['login'];

    public function mostrar(): void
    {
        $this->usuario = Sesion::usuario();
        if ($this->usuario) {
            Respuesta::redirigir('index.php?p=' . Menu::inicio($this->usuario['rol']));
        }
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store');
        echo $this->cabeza($this->t('tagline'), 'login') . $this->cuerpo() . $this->pie($this->scripts);
    }

    protected function cuerpo(): string
    {
        $actual = Idioma::actual();
        ob_start();
        ?>
<main class="login">
  <form class="login-box" id="form" action="#" novalidate>
    <div class="brand"><div class="logo" style="width:40px;height:40px;flex:0 0 40px;border-radius:50%;background:transparent;overflow:hidden"><img src="img/logo_menu.png" alt="Logo" width="40" height="40" style="display:block;width:100%;height:100%;object-fit:cover"></div>StockFlow<span></span></div>
    <p class="sub"><?= $this->e($this->t('tagline')) ?></p>
    <label for="email"><?= $this->e($this->t('email')) ?></label>
    <input id="email" name="email" type="email" required autocomplete="username">
    <label for="pass"><?= $this->e($this->t('pass')) ?></label>
    <input id="pass" name="password" type="password" required autocomplete="current-password">
    <button class="btn full" id="entrar"><?= $this->e($this->t('enter')) ?></button>
    <div class="msg" id="msg" role="alert"></div>
    <label for="lang">Idioma / Language</label>
    <select id="lang" data-idioma>
      <option value="es"<?= $actual === 'es' ? ' selected' : '' ?>>Espanol</option>
      <option value="en"<?= $actual === 'en' ? ' selected' : '' ?>>English</option>
    </select>
    <p class="foot"><?= $this->e(Config::get('empresa')) ?> · CETP-UTU · BT Informatica 2026</p>
  </form>
</main>
<?php
        return (string)ob_get_clean();
    }
}
