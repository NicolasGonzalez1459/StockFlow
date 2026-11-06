<?php

final class VentasPagina extends Pagina
{
    protected string $slug = 'ventas';
    protected string $tituloClave = 'sales';
    protected string $css = 'ventas';
    protected array $roles = ['admin', 'vendedor'];
    protected array $scripts = ['crud', 'carrito', 'ventas'];

    protected function cuerpo(): string
    {
        $canales = ['tienda_online', 'marketplace', 'redes', 'presencial'];
        ob_start();
        ?>
    <div class="panel">
      <h2><?= $this->e($this->t('register')) ?></h2>
      <div class="row">
        <div><label for="vcli"><?= $this->e($this->t('client')) ?></label><input id="vcli" maxlength="120"></div>
        <div><label for="vcanal"><?= $this->e($this->t('channel')) ?></label><select id="vcanal"><?php foreach ($canales as $c) { ?><option><?= $this->e($c) ?></option><?php } ?></select></div>
        <div><label for="vprod"><?= $this->e($this->t('product')) ?></label><select id="vprod"></select></div>
        <div><label for="vcant"><?= $this->e($this->t('qty')) ?></label><input id="vcant" type="number" min="1" value="1"></div>
        <div style="flex:0"><button class="btn sec" type="button" id="agregar"><?= $this->e($this->t('add')) ?></button></div>
      </div>
      <table style="margin-top:14px"><tbody id="car"></tbody></table>
      <div class="row" style="margin-top:12px">
        <div class="right"><b><?= $this->e($this->t('total')) ?></b>: <span id="tot">$0,00</span></div>
        <div style="flex:0"><button class="btn" type="button" id="guardar"><?= $this->e($this->t('save')) ?></button></div>
      </div>
    </div>
    <div class="panel">
      <h2><?= $this->e($this->t('sales')) ?></h2>
      <table><thead><tr><th>#</th><th><?= $this->e($this->t('date')) ?></th><th><?= $this->e($this->t('client')) ?></th><th><?= $this->e($this->t('channel')) ?></th>
      <th><?= $this->e($this->t('seller')) ?></th><th><?= $this->e($this->t('total')) ?></th><th><?= $this->e($this->t('status')) ?></th><th><?= $this->e($this->t('actions')) ?></th></tr></thead>
      <tbody id="tabla"></tbody></table>
    </div>
    <div class="modal" id="md"><div class="box">
      <h2><?= $this->e($this->t('detail')) ?></h2>
      <table><thead><tr><th><?= $this->e($this->t('product')) ?></th><th><?= $this->e($this->t('qty')) ?></th><th><?= $this->e($this->t('price')) ?></th><th><?= $this->e($this->t('total')) ?></th></tr></thead><tbody id="det"></tbody></table>
      <button class="btn sec" type="button" id="cerrar" data-cerrar="md" style="margin-top:14px"><?= $this->e($this->t('cancel')) ?></button>
    </div></div>
<?php
        return (string)ob_get_clean();
    }
}
