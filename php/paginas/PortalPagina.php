<?php

final class PortalPagina extends Pagina
{
    protected string $slug = 'portal';
    protected string $tituloClave = 'portal';
    protected string $css = 'portal';
    protected array $roles = ['cliente', 'admin'];
    protected array $scripts = ['crud', 'carrito', 'portal'];

    protected function cuerpo(): string
    {
        ob_start();
        ?>
    <div class="panel" id="pnlCompra">
      <div class="row" style="align-items:center;justify-content:space-between;margin-bottom:14px">
        <h2 style="margin:0"><?= $this->e($this->t('buyHere')) ?></h2>
        <div style="flex:0"><button class="btn sec" type="button" id="verCarrito" data-abrir="mc">🛒 <span id="carritoCant">0</span></button></div>
      </div>
      <div id="catalogo" class="catalogo"></div>
    </div>

    <div class="modal" id="mc"><div class="box">
      <h2><?= $this->e($this->t('cart')) ?></h2>
      <div id="carritoLista" class="cart-list"></div>
      <div class="cart-total">
        <span><?= $this->e($this->t('total')) ?></span>
        <strong id="carritoTotal">$0,00</strong>
      </div>
      <div class="row" style="margin-top:16px">
        <button class="btn" type="button" id="confirmarCompra"><?= $this->e($this->t('confirmBuy')) ?></button>
        <button class="btn sec" type="button" id="cerrarCarrito" data-cerrar="mc"><?= $this->e($this->t('cancel')) ?></button>
      </div>
    </div></div>

    <div class="panel">
      <h2><?= $this->e($this->t('myOrders')) ?></h2>
      <table><thead><tr><th>#</th><th><?= $this->e($this->t('date')) ?></th><th><?= $this->e($this->t('channel')) ?></th><th><?= $this->e($this->t('total')) ?></th><th><?= $this->e($this->t('status')) ?></th></tr></thead>
      <tbody id="tabla"></tbody></table>
    </div>
<?php
        return (string)ob_get_clean();
    }
}
