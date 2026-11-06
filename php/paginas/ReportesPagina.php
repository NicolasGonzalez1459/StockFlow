<?php

final class ReportesPagina extends Pagina
{
    protected string $slug = 'reportes';
    protected string $tituloClave = 'reports';
    protected string $css = 'reportes';
    protected array $roles = ['admin'];
    protected array $scripts = ['reportes'];

    private const TIPOS = ['ventas' => 'repSales', 'stock' => 'repStock', 'gastos' => 'repExpenses', 'rentabilidad' => 'repProfit'];

    protected function cuerpo(): string
    {
        $desde = date('Y-01-01');
        $hasta = date('Y-m-d');
        ob_start();
        ?>
    <form class="panel row" id="frep" action="#">
      <div><label for="rtipo"><?= $this->e($this->t('reports')) ?></label>
        <select id="rtipo" name="tipo">
          <?php foreach (self::TIPOS as $valor => $clave) { ?><option value="<?= $this->e($valor) ?>"><?= $this->e($this->t($clave)) ?></option><?php } ?>
        </select>
      </div>
      <div><label for="desde"><?= $this->e($this->t('from')) ?></label><input type="date" id="desde" name="desde" value="<?= $this->e($desde) ?>"></div>
      <div><label for="hasta"><?= $this->e($this->t('to')) ?></label><input type="date" id="hasta" name="hasta" value="<?= $this->e($hasta) ?>"></div>
      <div style="flex:0"><button class="btn" id="aplicar"><?= $this->e($this->t('apply')) ?></button></div>
      <div style="flex:0"><button class="btn sec" type="button" id="exportar"><?= $this->e($this->t('export')) ?></button></div>
    </form>
    <div class="panel"><table><thead><tr id="head"></tr></thead><tbody id="body"></tbody></table></div>
<?php
        return (string)ob_get_clean();
    }
}
