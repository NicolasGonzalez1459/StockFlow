<?php

final class DashboardPagina extends Pagina
{
    protected string $slug = 'dashboard';
    protected string $tituloClave = 'dashboard';
    protected string $css = 'dashboard';
    protected array $roles = ['admin', 'repositor'];
    protected array $scripts = ['crud', 'grafico', 'dashboard'];

    protected function cuerpo(): string
    {
        $esAdmin = $this->usuario['rol'] === 'admin';
        $desde = date('Y-01-01');
        $hasta = date('Y-m-d');
        ob_start();
        if ($esAdmin) {
            ?>
    <form class="panel row" id="ffiltro" action="#">
      <div><label for="desde"><?= $this->e($this->t('from')) ?></label><input type="date" id="desde" name="desde" value="<?= $this->e($desde) ?>"></div>
      <div><label for="hasta"><?= $this->e($this->t('to')) ?></label><input type="date" id="hasta" name="hasta" value="<?= $this->e($hasta) ?>"></div>
      <div style="flex:0"><button class="btn" id="aplicar"><?= $this->e($this->t('apply')) ?></button></div>
    </form>
    <div class="cards">
      <div class="card"><div class="k"><?= $this->e($this->t('salesTotal')) ?></div><div class="v" id="kVentas">$0,00</div></div>
      <div class="card"><div class="k"><?= $this->e($this->t('salesCount')) ?></div><div class="v" id="kCant">0</div></div>
      <div class="card"><div class="k"><?= $this->e($this->t('expensesTotal')) ?></div><div class="v" id="kGastos">$0,00</div></div>
      <div class="card"><div class="k"><?= $this->e($this->t('margin')) ?></div><div class="v" id="kMargen">$0,00</div></div>
      <div class="card"><div class="k"><?= $this->e($this->t('critical')) ?></div><div class="v bad" id="kCritico">0</div></div>
    </div>
    <div class="grid3" id="boxKpiCharts">
      <div class="panel"><h2><?= $this->e($this->t('evolution')) ?></h2>
        <div class="chart-bars" id="chartSerie"></div>
        <div class="chart-labels" id="chartSerieLbl"></div>
      </div>
      <div class="panel"><h2><?= $this->e($this->t('composition')) ?></h2>
        <div class="donut-wrap" id="donutComposicion"></div>
      </div>
    </div>
    <div class="panel" id="boxTop"><h2><?= $this->e($this->t('topProducts')) ?></h2>
      <div class="donut-wrap" id="donutTop"></div>
    </div>
    <div class="panel" id="boxNotis"><h2><?= $this->e($this->t('notifications')) ?></h2><table><tbody id="notis"></tbody></table></div>
<?php
        }
        ?>
    <div class="panel" id="boxmov">
      <h2><?= $this->e($this->t('movements')) ?></h2>
      <form id="fm" class="row" data-recurso="movimientos" action="#">
        <div><label for="mprod"><?= $this->e($this->t('product')) ?></label><select id="mprod" name="producto_id"></select></div>
        <div><label for="mtipo"><?= $this->e($this->t('type')) ?></label><select id="mtipo" name="tipo"><option value="ingreso">+</option><option value="egreso">-</option><option value="ajuste">=</option></select></div>
        <div><label for="mcant"><?= $this->e($this->t('qty')) ?></label><input id="mcant" name="cantidad" type="number" min="0" value="1" required></div>
        <div><label for="mmot"><?= $this->e($this->t('reason')) ?></label><input id="mmot" name="motivo" maxlength="200"></div>
        <div style="flex:0"><button class="btn"><?= $this->e($this->t('save')) ?></button></div>
      </form>
    </div>
    <div class="panel"><h2><?= $this->e($this->t('movements')) ?></h2>
      <table><thead><tr><th><?= $this->e($this->t('date')) ?></th><th><?= $this->e($this->t('product')) ?></th><th><?= $this->e($this->t('type')) ?></th><th><?= $this->e($this->t('qty')) ?></th><th><?= $this->e($this->t('stock')) ?></th><th><?= $this->e($this->t('user')) ?></th></tr></thead>
      <tbody id="movs"></tbody></table>
    </div>
<?php
        return (string)ob_get_clean();
    }
}
