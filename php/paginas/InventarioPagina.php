<?php

final class InventarioPagina extends Pagina
{
    protected string $slug = 'inventario';
    protected string $tituloClave = 'inventory';
    protected string $css = 'inventario';
    protected array $roles = ['admin', 'repositor', 'vendedor'];
    protected array $scripts = ['crud', 'inventario'];

    protected function cuerpo(): string
    {
        $esAdmin = $this->usuario['rol'] === 'admin';
        ob_start();
        ?>
    <form class="panel row" id="fbuscar" action="#">
      <div><label for="buscar"><?= $this->e($this->t('search')) ?></label><input id="buscar" name="q" placeholder="<?= $this->e($this->t('search')) ?>" autocomplete="off"></div>
      <div style="flex:0"><button class="btn sec"><?= $this->e($this->t('search')) ?></button></div>
      <?php if ($esAdmin) { ?><div style="flex:0"><button class="btn" type="button" id="nuevo" data-abrir="mp"><?= $this->e($this->t('new')) ?></button></div><?php } ?>
    </form>
    <div class="panel">
      <table>
        <thead><tr><th><?= $this->e($this->t('photo')) ?></th><th><?= $this->e($this->t('code')) ?></th><th><?= $this->e($this->t('name')) ?></th><th><?= $this->e($this->t('category')) ?></th>
        <th><?= $this->e($this->t('cost')) ?></th><th><?= $this->e($this->t('price')) ?></th><th><?= $this->e($this->t('stock')) ?></th><th><?= $this->e($this->t('min')) ?></th><th><?= $this->e($this->t('actions')) ?></th></tr></thead>
        <tbody id="tabla"></tbody>
      </table>
    </div>
    <div class="panel"><h2><?= $this->e($this->t('alerts')) ?></h2>
      <table><thead><tr><th><?= $this->e($this->t('code')) ?></th><th><?= $this->e($this->t('name')) ?></th><th><?= $this->e($this->t('stock')) ?></th><th><?= $this->e($this->t('min')) ?></th></tr></thead>
      <tbody id="alertas"></tbody></table>
    </div>
    <?php if ($esAdmin) { ?>
    <div class="modal" id="mp"><form class="box" id="fp" data-recurso="productos" data-solo-crear="stock">
      <h2><?= $this->e($this->t('inventory')) ?></h2>
      <label for="codigo"><?= $this->e($this->t('code')) ?></label><input id="codigo" name="codigo" maxlength="40" required>
      <label for="nombre"><?= $this->e($this->t('name')) ?></label><input id="nombre" name="nombre" maxlength="120" required>
      <label for="cat"><?= $this->e($this->t('category')) ?></label><select id="cat" name="categoria_id"><option value="">-</option></select>
      <label for="unidad"><?= $this->e($this->t('unit')) ?></label><input id="unidad" name="unidad" value="unidad" maxlength="20">
      <label for="costo"><?= $this->e($this->t('cost')) ?></label><input id="costo" name="precio_costo" type="number" step="0.01" min="0" value="0" required>
      <label for="precio"><?= $this->e($this->t('price')) ?></label><input id="precio" name="precio_venta" type="number" step="0.01" min="0" value="0" required>
      <label for="stock"><?= $this->e($this->t('stock')) ?></label><input id="stock" name="stock" type="number" min="0" value="0">
      <label for="minimo"><?= $this->e($this->t('min')) ?></label><input id="minimo" name="stock_minimo" type="number" min="0" value="5" required>
      <label for="imgFile"><?= $this->e($this->t('photo')) ?></label>
      <div class="img-row">
        <img id="imgPrev" class="img-prev" alt="" style="display:none" data-src-campo="imagen">
        <input id="imgFile" type="file" name="imagen" accept="image/png,image/jpeg,image/webp" data-preview="imgPrev">
      </div>
      <div class="row" style="margin-top:16px">
        <button class="btn"><?= $this->e($this->t('save')) ?></button>
        <button class="btn sec" type="button" id="cerrar" data-cerrar="mp"><?= $this->e($this->t('cancel')) ?></button>
      </div>
    </form></div>
    <?php } ?>
<?php
        return (string)ob_get_clean();
    }
}
