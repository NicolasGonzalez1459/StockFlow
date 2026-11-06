<?php

final class UsuariosPagina extends Pagina
{
    protected string $slug = 'usuarios';
    protected string $tituloClave = 'users';
    protected string $css = 'usuarios';
    protected array $roles = ['admin'];
    protected array $scripts = ['crud', 'usuarios'];

    protected function cuerpo(): string
    {
        ob_start();
        ?>
    <div class="panel row">
      <div style="flex:0"><button class="btn" type="button" id="nuevo" data-abrir="mu"><?= $this->e($this->t('new')) ?></button></div>
    </div>
    <div class="panel">
      <table><thead><tr><th>#</th><th><?= $this->e($this->t('name')) ?></th><th><?= $this->e($this->t('email')) ?></th><th><?= $this->e($this->t('role')) ?></th><th><?= $this->e($this->t('active')) ?></th><th><?= $this->e($this->t('actions')) ?></th></tr></thead>
      <tbody id="tabla"></tbody></table>
    </div>
    <div class="modal" id="mu"><form class="box" id="fu" data-recurso="usuarios">
      <h2><?= $this->e($this->t('users')) ?></h2>
      <label for="unombre"><?= $this->e($this->t('name')) ?></label><input id="unombre" name="nombre" maxlength="100" required>
      <label for="uemail"><?= $this->e($this->t('email')) ?></label><input id="uemail" name="email" type="email" maxlength="150" required>
      <label for="upass"><?= $this->e($this->t('pass')) ?></label><input id="upass" name="password" type="password" minlength="6" maxlength="100" autocomplete="new-password">
      <label for="urol"><?= $this->e($this->t('role')) ?></label><select id="urol" name="rol">
        <?php foreach (UsuarioDAO::ROLES as $r) { ?><option value="<?= $this->e($r) ?>"<?= $r === 'vendedor' ? ' selected' : '' ?>><?= $this->e($r) ?></option><?php } ?>
      </select>
      <label for="uact"><?= $this->e($this->t('active')) ?></label><select id="uact" name="activo"><option value="1">1</option><option value="0">0</option></select>
      <div class="row" style="margin-top:16px">
        <button class="btn"><?= $this->e($this->t('save')) ?></button>
        <button class="btn sec" type="button" id="cerrar" data-cerrar="mu"><?= $this->e($this->t('cancel')) ?></button>
      </div>
    </form></div>
<?php
        return (string)ob_get_clean();
    }
}
