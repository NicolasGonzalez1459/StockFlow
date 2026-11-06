(() => {
  const esAdmin = Contexto.rol === 'admin';
  const movs = document.getElementById('movs');
  const producto = document.getElementById('mprod');

  const pintarMovimientos = lista => {
    movs.replaceChildren(...(lista.length
      ? lista.map(m => h('tr', {},
          h('td', { texto: fechaCorta(m.fecha) }),
          h('td', { texto: m.producto || '-' }),
          h('td', { texto: m.tipo }),
          h('td', { texto: m.cantidad }),
          h('td', { texto: m.stock_resultante }),
          h('td', { texto: m.usuario || '-' })))
      : [h('tr', {}, h('td', { colspan: '6', texto: T('noData') }))]));
  };

  const cargarMovimientos = () => Api.obtener('movimientos.php?limite=20').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    pintarMovimientos(data.data);
  });

  const cargarProductos = () => Api.obtener('productos.php').then(data => {
    if (data.status !== 'success') {
      Api.falla(data);
      return;
    }
    producto.replaceChildren(...data.data.map(p => h('option', { value: p.id, texto: p.nombre })));
  });

  let despues = () => cargarMovimientos();

  if (esAdmin) {
    const notis = document.getElementById('notis');
    const filtro = document.getElementById('ffiltro');
    const kMargen = document.getElementById('kMargen');

    const pintarNotificaciones = lista => {
      notis.replaceChildren(...(lista.length
        ? lista.map(n => h('tr', { class: Number(n.leida) ? 'leida' : '' },
            h('td', { texto: fechaCorta(n.fecha) }),
            h('td', { texto: n.mensaje }),
            h('td', {}, Number(n.leida) ? '' : h('button', { class: 'btn sec sm', type: 'button', texto: T('markRead'), onclick: () => marcar(n.id) }))))
        : [h('tr', {}, h('td', { colspan: '3', texto: T('noData') }))]));
    };

    const cargarNotificaciones = () => Api.obtener('notificaciones.php?limite=6').then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return;
      }
      pintarNotificaciones(data.data);
    });

    const marcar = id => Api.actualizar('notificaciones.php?id=' + id, {}).then(data => {
      if (data.status !== 'success') {
        Api.falla(data);
        return;
      }
      return cargarNotificaciones();
    });

    const cargarReporte = () => {
      const consulta = Api.consulta('reportes.php', {
        tipo: 'dashboard',
        desde: document.getElementById('desde').value,
        hasta: document.getElementById('hasta').value
      });
      return Api.obtener(consulta).then(data => {
        if (data.status !== 'success') {
          Api.falla(data);
          return;
        }
        const d = data.data;
        document.getElementById('kVentas').textContent = dinero(d.ventas_total);
        document.getElementById('kCant').textContent = d.ventas_cantidad;
        document.getElementById('kGastos').textContent = dinero(d.gastos_total);
        kMargen.textContent = dinero(d.margen);
        kMargen.className = 'v ' + (d.margen >= 0 ? 'ok' : 'bad');
        document.getElementById('kCritico').textContent = d.stock_critico;
        Grafico.barras(document.getElementById('chartSerie'), document.getElementById('chartSerieLbl'), d.serie);
        Grafico.dibujarDona(document.getElementById('donutComposicion'), [
          { etiqueta: T('costs'), valor: Number(d.costo_total), color: Grafico.colores[3] },
          { etiqueta: T('expensesTotal'), valor: Number(d.gastos_total), color: Grafico.colores[2] },
          { etiqueta: T('margin'), valor: Math.max(0, d.margen), color: Grafico.colores[1] }
        ], 130, dinero);
        Grafico.dibujarDona(document.getElementById('donutTop'), d.top.map((x, i) => ({
          etiqueta: String(x.nombre),
          valor: Number(x.cant),
          color: Grafico.colores[i % Grafico.colores.length]
        })), 150, v => parseInt(v, 10) + ' u.');
      });
    };

    filtro.addEventListener('submit', e => {
      e.preventDefault();
      cargarReporte();
    });

    despues = () => Promise.all([cargarMovimientos(), cargarNotificaciones(), cargarReporte()]);
    cargarReporte();
    cargarNotificaciones();
  }

  Crud.registrar('movimientos', { endpoint: 'movimientos.php', despues: () => despues() });

  cargarProductos();
  cargarMovimientos();
})();
