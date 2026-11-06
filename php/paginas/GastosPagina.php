<?php

final class GastosPagina extends Pagina
{
    protected string $slug = 'gastos';
    protected string $tituloClave = 'expenses';
    protected string $css = 'gastos';
    protected array $roles = ['admin'];
    protected array $scripts = ['crud', 'gastos'];

    protected function cuerpo(): string
    {
        ob_start();
        ?>
    <div class="panel">
      <h2><?= $this->e($this->t('expenses')) ?></h2>
      <form class="row" id="fg" data-recurso="gastos" action="#">
        <div><label for="gcat"><?= $this->e($this->t('category')) ?></label><select id="gcat" name="categoria_id"></select></div>
        <div><label for="gdesc"><?= $this->e($this->t('description')) ?></label><input id="gdesc" name="descripcion" maxlength="200" required></div>
        <div><label for="gmonto"><?= $this->e($this->t('amount')) ?></label><input id="gmonto" name="monto" type="number" step="0.01" min="0.01" required></div>
        <div><label for="gfec"><?= $this->e($this->t('date')) ?></label><input id="gfec" name="fecha" type="date" value="<?= $this->e(date('Y-m-d')) ?>" required></div>
        <div style="flex:0"><button class="btn"><?= $this->e($this->t('save')) ?></button></div>
        <div style="flex:0"><button class="btn sec" type="button" id="cancelar" hidden><?= $this->e($this->t('edit_cancel')) ?></button></div>
      </form>
    </div>
    <div class="panel">
      <table><thead><tr><th><?= $this->e($this->t('date')) ?></th><th><?= $this->e($this->t('category')) ?></th><th><?= $this->e($this->t('description')) ?></th><th><?= $this->e($this->t('amount')) ?></th><th><?= $this->e($this->t('actions')) ?></th></tr></thead>
      <tbody id="tabla"></tbody></table>
      <p class="right" style="margin-top:10px"><b><?= $this->e($this->t('total')) ?></b>: <span id="total">$0,00</span></p>
    </div>
<?php
        return (string)ob_get_clean();
    }
}
